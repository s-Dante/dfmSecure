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
        $type = fake()-> randomElement([
            SinisterMultimediaTypeEnum::PHOTO->value,
            SinisterMultimediaTypeEnum::VIDEO->value,
        ]);

        $typeDirectoryMap = [
            SinisterMultimediaTypeEnum::PHOTO->value => 'photos',
            SinisterMultimediaTypeEnum::VIDEO->value => 'videos',
            SinisterMultimediaTypeEnum::AUDIO->value => 'audios',
            SinisterMultimediaTypeEnum::DOCUMENT->value => 'documents',
        ];

        $path = 'database/sinister/multimedia';
        $basePath = public_path($path);

        $subDir = $typeDirectoryMap[$type];
        $directory = $basePath . '/' . $subDir;

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $files = File::files($directory);

        if(empty($files)) {
            return [
                'type' => $type,
                'blob_file' => null,
                'path_file' => null,
                'description' => fake()->sentence(),
                'mime' => null,
                'size' => 0,
                'thumbnail' => null,
                'sinister_id' => Sinister::factory(),
            ];
        }

        $file = fake()->randomElement($files);
        $fileName = $file->getFilename();

        $mime = mime_content_type($file->getPathname()) ?: 'application/octet-stream';
        $size = filesize($file->getPathname()) ?: 0;

        $isBlob = $size < 5000000 ? fake()->boolean() : false;

        return [
                'type' => $type,
                'blob_file' => $isBlob ? file_get_contents($file->getPathname()) : null,
                'path_file' => $isBlob ? null : $path . '/' . $subDir . '/' . $fileName,
                'description' => fake()->sentence(),
                'mime' => $mime,
                'size' => $size,
                'thumbnail' => null,
                'sinister_id' => Sinister::factory(),
            ];
    }
}
