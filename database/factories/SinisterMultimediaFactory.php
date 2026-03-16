<?php

namespace Database\Factories;

use App\Enums\SinisterMultimediaTypeEnum;
use App\Models\Sinister;
use App\Models\SinisterMultimedia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SinisterMultimedia>
 */
class SinisterMultimediaFactory extends Factory
{
    protected $model = SinisterMultimedia::class;

    /**
     * Mime types and extensions keyed by SinisterMultimediaTypeEnum value.
     */
    private const TYPE_CONFIG = [
        SinisterMultimediaTypeEnum::PHOTO->value => [
            'mimes'     => ['image/jpeg', 'image/png', 'image/webp'],
            'ext'       => ['jpg', 'png', 'webp'],
            'dir'       => 'sinisters/photos',
        ],
        SinisterMultimediaTypeEnum::VIDEO->value => [
            'mimes'     => ['video/mp4', 'video/quicktime'],
            'ext'       => ['mp4', 'mov'],
            'dir'       => 'sinisters/videos',
        ],
        SinisterMultimediaTypeEnum::DOCUMENT->value => [
            'mimes'     => ['application/pdf', 'application/msword'],
            'ext'       => ['pdf', 'docx'],
            'dir'       => 'sinisters/documents',
        ],
        SinisterMultimediaTypeEnum::AUDIO->value => [
            'mimes'     => ['audio/mpeg', 'audio/wav'],
            'ext'       => ['mp3', 'wav'],
            'dir'       => 'sinisters/audios',
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Prioridad a fotos y videos
        $type   = fake()->randomElement([SinisterMultimediaTypeEnum::PHOTO->value, SinisterMultimediaTypeEnum::VIDEO->value]);
        $config = self::TYPE_CONFIG[$type];
        
        $baseDir = 'database/sinister/multimedia/' . $config['dir'];
        $absoluteDir = public_path($baseDir);
        
        if (File::exists($absoluteDir)) {
            $files = File::files($absoluteDir);
        } else {
            $files = [];
        }

        if (count($files) > 0) {
            $file = fake()->randomElement($files);
            $path = $baseDir . '/' . $file->getFilename();
            $mime = mime_content_type($file->getPathname()) ?: 'application/octet-stream';
            $size = $file->getSize();
            // Extraer info de thumbnail (falsa para el ejemplo si es que no existe uno real)
            $thumbnail = 'database/sinister/multimedia/thumbnails/thumb_' . $file->getFilename();
            $blobContent = file_get_contents($file->getPathname());
        } else {
            // Fallback en caso de que la carpeta esté vacía
            $mime   = fake()->randomElement($config['mimes']);
            $ext    = $config['ext'][array_search($mime, $config['mimes'])];
            $path   = $baseDir . '/' . fake()->uuid() . '.' . $ext;
            $size   = fake()->numberBetween(50_000, 5_000_000);
            $thumbnail = 'database/sinister/multimedia/thumbnails/' . fake()->uuid() . '.jpg';
            $blobContent = null;
        }

        // Aleatoriamente decide si guardar como blob o como path (o ambos, dependiendo tu DB)
        $saveAsBlob = fake()->boolean(40); // 40% probabilidad de guardar solo blob

        return [
            'type'        => $type,
            'blob_file'   => $saveAsBlob ? $blobContent : null,
            'path_file'   => !$saveAsBlob ? $path : null,
            'description' => fake()->optional(0.6)->sentence(),
            'mime'        => $mime,
            'size'        => $size,
            'thumbnail'   => $thumbnail,
            'sinister_id' => Sinister::inRandomOrder()->first()?->id ?? Sinister::factory(),
        ];
    }
}

