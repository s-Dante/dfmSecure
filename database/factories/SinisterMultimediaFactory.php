<?php

namespace Database\Factories;

use App\Enums\SinisterMultimediaTypeEnum;
use App\Models\Sinister;
use App\Models\SinisterMultimedia;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $type   = fake()->randomElement(SinisterMultimediaTypeEnum::values());
        $config = self::TYPE_CONFIG[$type];
        $mime   = fake()->randomElement($config['mimes']);
        $ext    = $config['ext'][array_search($mime, $config['mimes'])];
        $path   = $config['dir'] . '/' . fake()->uuid() . '.' . $ext;

        return [
            'type'        => $type,
            'blob_file'   => null, // Binary not stored during seeding
            'path_file'   => $path,
            'description' => fake()->optional(0.6)->sentence(),
            'mime'        => $mime,
            'size'        => fake()->numberBetween(50_000, 5_000_000),
            'thumbnail'   => in_array($type, [SinisterMultimediaTypeEnum::PHOTO->value, SinisterMultimediaTypeEnum::VIDEO->value])
                ? 'sinisters/thumbnails/' . fake()->uuid() . '.jpg'
                : null,
            'sinister_id' => Sinister::inRandomOrder()->first()?->id ?? Sinister::factory(),
        ];
    }
}

