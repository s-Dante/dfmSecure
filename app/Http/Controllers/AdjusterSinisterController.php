<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\Policy;
use App\Models\Sinister;

use App\Enums\SinisterStatusEnum;
use App\Enums\PolicyStatusEnum;
use App\Enums\SinisterMultimediaTypeEnum;
use App\Models\SinisterMultimedia;

class AdjusterSinisterController extends Controller
{
    public function create(Request $request)
    {
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
            'description' => 'required|string',
        ]);

        $sinister = Sinister::create([
            'folio' => Str::uuid(),
            'occur_date' => $validated['occur_date'],
            'report_date' => now(),
            'description' => $validated['description'],
            'location' => $validated['location'],
            'status' => SinisterStatusEnum::REPORTED,
            'adjuster_id' => $user->id,
            'policy_id' => $validated['policy_id'],
        ]);

        return response()->json([
            'success' => true,
            'sinister_id' => $sinister->id,
            'message' => 'Sinistero creado exitosamente'
        ]);
    }

    public function uploadMedia(Request $request)
    {
        $validated = $request->validate([
            'sinister_id' => 'required|exists:sinisters,id',
            'file' =>  'required|file',
            'storage_type' => 'required|in:url,blob'
        ]);

        $file = $validated['file'];
        $storageType = $validated['storage_type'];

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
            $blobFile = file_get_contents($file->getRealPath());
        } else {
            $pathFile = $file->store('sinisters', 'public');
        }

        SinisterMultimedia::create([
            'type' => $typeEnum,
            'blob_file' => $blobFile,
            'path_file' => $pathFile,
            'description' => $file->getClientOrOriginalName(),
            'mime' => $mime,
            'size' => $file->getSize(),
            'sinister_id' => $validated['sinister_id']
        ]);

        return response()->json([
            'succes' => true,
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
            'storage_type' => 'required|in:url,blob'
        ]);

        $chunk = $request->file('chunk');
        $chunkIndex = (int) $request->input('chunk_index');
        $totalChunks = (int) $request->input('total_chunks');
        $fileName = $request->input('file_name');
        $storageType = $request->input('storage_type');
        $sinisterId = $request->input('sinister_id');

        $chunkDir = storage_path('app/chunks/' . $sinisterId . '/' . md5($fileName));

        if (!is_dir($chunkDir)) {
            mkdir($chunkDir, 0777, true);
        }

        $chunk->move($chunkDir, $chunkIndex);
        $chunksCount = count(glob($chunkDir . '/*'));

        if ($chunksCount === $totalChunks) {
            $finalFilePath = $chunkDir . '/' . $fileName;
            $finalFile = fopen($finalFilePath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPartPath = $chunkDir . '/' . $i;
                $chunkContent = file_get_contents($chunkPartPath);
                fwrite($finalFile, $chunkContent);
                unlink($chunkPartPath);
            }
            fclose($finalFile);

            $mime = mime_content_type($finalFilePath) ?: 'application/octet-stream';

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
                $destFolder = 'public/sinisters';
                $storedName = Str::random(40) . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
                $finalDest = storage_path('app/' . $destFolder . '/' . $storedName);

                if (!is_dir(dirname($finalDest))) {
                    mkdir(dirname($finalDest), 0777, true);
                }

                copy($finalFilePath, $finalDest);

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

            unlink($finalFilePath);
            rmdir($chunkDir);

            return response()->json([
                'success' => true,
                'message' => 'Archivo subido'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Chunk ' . $chunkIndex . ' recibido correctamente'
        ]);
    }

    public function edit(Request $request, $id)
    {
        $sinister = Sinister::with(['policy.vehicle.vehicleModel', 'policy.insured', 'multimedia'])
            ->findOrFail($id);

        return view('adjuster.sinister-edit', compact('sinister'));
    }

    public function update(Request $request, $id)
    {
        $sinister = Sinister::findOrFail($id);

        $validated = $request->validate([
            'occur_date' => 'required|date|before_or_equal:today',
            'report_date' => 'required|date|before_or_equal:today',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $sinister->update([
            'occur_date' => $validated['occur_date'],
            'report_date' => $validated['report_date'],
            'location' => $validated['location'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('sinisterDetail', $sinister->id)->with('success', 'Siniestro actualizado exitosamente.');
    }

    public function deleteMedia(Request $request, $id)
    {
        $media = SinisterMultimedia::findOrFail($id);
        
        if (!empty($media->path_file) && \Storage::disk('public')->exists($media->path_file)) {
            \Storage::disk('public')->delete($media->path_file);
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Archivo eliminado correctamente'
        ]);
    }
}
