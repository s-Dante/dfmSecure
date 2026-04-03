<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Policy;
use App\Models\Sinister;
use App\Models\SinisterMultimedia;
use App\Enums\PolicyStatusEnum;
use App\Enums\SinisterStatusEnum;
use App\Enums\SinisterMultimediaTypeEnum;

class AdjusterSinisterController extends Controller
{
    public function create(Request $request)
    {
        // Traer pólizas activas para mostrarlas en el select. 
        // Idealmente en una BD grande se filtraría por rol o paginación, 
        // pero por ahora traemos las activas.
        $policies = Policy::with(['vehicle.vehicleModel', 'insured'])
            ->where('status', PolicyStatusEnum::ACTIVE)
            ->get();
            
        return view('adjuster.sinister-register', compact('policies'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'policy_id' => 'required|exists:policies,id',
            'occur_date' => 'required|date|before_or_equal:today',
            'location' => 'required|string|max:255',
            'description' => 'required|string'
        ]);

        // Crear el siniestro general
        $sinister = Sinister::create([
            'folio' => Str::uuid(),
            'occur_date' => $validated['occur_date'],
            'report_date' => now(),
            'description' => $validated['description'],
            'location' => $validated['location'],
            'status' => SinisterStatusEnum::REPORTED,
            'adjuster_id' => $user->id,
            'policy_id' => $validated['policy_id']
        ]);

        return response()->json([
            'success' => true,
            'sinister_id' => $sinister->id,
            'message' => 'Siniestro creado base. Preparado para subida multimedia.'
        ]);
    }

    public function uploadMedia(Request $request)
    {
        // Este endpoint es llamado vía Fetch asincrónicamente por cada archivo
        $validated = $request->validate([
            'sinister_id' => 'required|exists:sinisters,id',
            'file' => 'required|file',
            'storage_type' => 'required|in:url,blob',
        ]);

        $file = $validated['file'];
        $storageType = $validated['storage_type'];

        // Determinar el tipo (enum) en base al MIME Type básico
        $mime = $file->getMimeType();
        if (str_starts_with($mime, 'image/')) {
            $typeEnum = SinisterMultimediaTypeEnum::PHOTO;
        } elseif (str_starts_with($mime, 'video/')) {
            $typeEnum = SinisterMultimediaTypeEnum::VIDEO;
        } elseif (str_starts_with($mime, 'audio/')) {
            $typeEnum = SinisterMultimediaTypeEnum::AUDIO;
        } else {
            $typeEnum = SinisterMultimediaTypeEnum::DOCUMENT;
        }
        
        $pathFile = null;
        $blobFile = null;

        if ($storageType === 'blob') {
            // Cargar binario a RAM y asignarlo. Gracias a Vanilla JS pre-compresión, será ligero para imágenes.
            $blobFile = file_get_contents($file->getRealPath());
        } else {
            // Guardado mediante path normal dentro de la carpeta local storage/app/public/sinisters
            $pathFile = $file->store('sinisters', 'public');
        }

        // Generar registro multimedia asociado
        SinisterMultimedia::create([
            'type' => $typeEnum,
            'blob_file' => $blobFile,
            'path_file' => $pathFile,
            'description' => $file->getClientOriginalName(),
            'mime' => $mime,
            'size' => $file->getSize(),
            'sinister_id' => $validated['sinister_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Archivo subido correctamente'
        ]);
    }

    public function uploadChunk(Request $request)
    {
        $request->validate([
            'chunk' => 'required|file',
            'chunk_index' => 'required|integer',
            'total_chunks' => 'required|integer',
            'file_name' => 'required|string',
            'sinister_id' => 'required|exists:sinisters,id',
            'storage_type' => 'required|in:url,blob',
        ]);

        $chunk = $request->file('chunk');
        $chunkIndex = (int) $request->input('chunk_index');
        $totalChunks = (int) $request->input('total_chunks');
        $fileName = $request->input('file_name');
        $sinisterId = $request->input('sinister_id');
        $storageType = $request->input('storage_type');

        // Path local temporal basado en el siniestro y nombre de archivo para evitar colisiones
        $chunkDir = storage_path('app/chunks/' . $sinisterId . '/' . md5($fileName));

        if (!is_dir($chunkDir)) {
            mkdir($chunkDir, 0777, true);
        }

        // Mover el chunk con un nombre que asegure su orden (0, 1, 2...)
        $chunk->move($chunkDir, $chunkIndex);

        // Validar si hemos recibido el último bloque esperado
        // Atención: Puede que lleguen desordenados, contamos si tenemos la cantidad correcta.
        $chunksCount = count(glob($chunkDir . '/*'));
        
        if ($chunksCount === $totalChunks) {
            // Ensamblar el archivo final temporal
            $finalFilePath = $chunkDir . '/' . $fileName;
            $finalFile = fopen($finalFilePath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPartPath = $chunkDir . '/' . $i;
                $chunkContent = file_get_contents($chunkPartPath);
                fwrite($finalFile, $chunkContent);
                unlink($chunkPartPath); // Borrar chunk parcial
            }
            fclose($finalFile);

            // Re-evaluación del MIME original por compatibilidad web
            $mime = mime_content_type($finalFilePath) ?: 'application/octet-stream';
            
            // Determinar tipo enum
            if (str_starts_with($mime, 'image/')) {
                $typeEnum = SinisterMultimediaTypeEnum::PHOTO;
            } elseif (str_starts_with($mime, 'video/')) {
                $typeEnum = SinisterMultimediaTypeEnum::VIDEO;
            } elseif (str_starts_with($mime, 'audio/')) {
                $typeEnum = SinisterMultimediaTypeEnum::AUDIO;
            } else {
                $typeEnum = SinisterMultimediaTypeEnum::DOCUMENT;
            }

            $size = filesize($finalFilePath);

            $pathFile = null;
            $blobFile = null;

            if ($storageType === 'blob') {
                $blobFile = file_get_contents($finalFilePath);
            } else {
                // Guardarlo nativamente en el public disk pasándolo como UploadedFile simulado no es tan directo, 
                // pero podemos copiar el archivo directamente al public path.
                $destFolder = 'public/sinisters';
                $storedName = \Illuminate\Support\Str::random(40) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
                $finalDest = storage_path('app/' . $destFolder . '/' . $storedName);
                
                if(!is_dir(dirname($finalDest))) {
                    mkdir(dirname($finalDest), 0777, true);
                }
                copy($finalFilePath, $finalDest);
                
                // Generar un path relativo como haría Laravel
                $pathFile = 'sinisters/' . $storedName;
            }

            SinisterMultimedia::create([
                'type' => $typeEnum,
                'blob_file' => $blobFile,
                'path_file' => $pathFile,
                'description' => $fileName,
                'mime' => $mime,
                'size' => $size,
                'sinister_id' => $sinisterId
            ]);

            // Limpieza del archivo ensamblado y el directorio
            unlink($finalFilePath);
            rmdir($chunkDir);

            return response()->json([
                'success' => true,
                'message' => 'Archivo ensamblado y subido.'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Chunk ' . $chunkIndex . ' recibido correctamente.'
        ]);
    }
}
