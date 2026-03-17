<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Sinister;
use App\Models\SinisterMultimedia;
use App\Enums\SinisterMultimediaTypeEnum;
use Illuminate\Support\Facades\File;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SinisterMultimedia>
 */
class SinisterMultimediaFactory extends Factory
{
    protected $model = SinisterMultimedia::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement([
            SinisterMultimediaTypeEnum::PHOTO->value, 
            SinisterMultimediaTypeEnum::VIDEO->value
        ]);

        $typeDirectoryMap = [
            SinisterMultimediaTypeEnum::PHOTO->value => 'photos',
            SinisterMultimediaTypeEnum::VIDEO->value => 'videos',
            // Preparado para futuros tipos:
            // SinisterMultimediaTypeEnum::AUDIO->value => 'audios',
            // SinisterMultimediaTypeEnum::DOCUMENT->value => 'documents',
        ];

        $publicPath = 'database/sinister/multimedia';
        $fullPath = public_path($publicPath);
        
        $subDir = $typeDirectoryMap[$type];
        $directory = $fullPath . '/' . $subDir;

        // Asegurar que el directorio existe para evitar errores si no hay archivos aún
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $files = File::files($directory);
        
        if (empty($files)) {
            // Fallback en caso de que la carpeta esté vacía
            return [
                'type' => $type,
                'blob_file' => null,
                'path_file' => $publicPath . '/' . $subDir . '/placeholder.jpg',
                'description' => fake()->sentence(),
                'mime' => 'image/jpeg',
                'size' => 1024,
                'thumbnail' => null,
                'sinister_id' => Sinister::factory(),
            ];
        }

        $file = fake()->randomElement($files);
        $fileName = $file->getFilename();
        
        $mime = mime_content_type($file->getPathname()) ?: 'application/octet-stream';
        $size = filesize($file->getPathname()) ?: 0;
        
        // 50% de probabilidad de ser blob o url, a menos que el archivo sea enorme (>1MB) 
        // para evitar el agotamiento de la memoria de PHP durante el DB Seeding
        $isBlob = $size < 1000000 ? fake()->boolean() : false;

        return [
            'type' => $type,
            'blob_file' => $isBlob ? file_get_contents($file->getPathname()) : null,
            'path_file' => $isBlob ? null : $publicPath . '/' . $subDir . '/' . $fileName,
            'description' => fake()->sentence(),
            'mime' => $mime,
            'size' => $size,
            'thumbnail' => null,
            'sinister_id' => Sinister::factory(),
        ];
    }
}
