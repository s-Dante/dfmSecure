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

        

        return [
            'type' => $type,
            'blob_file' => ,
            'path_file' => ,
            'description' => ,
            'mime' => ,
            'size' => ,
            'thumbnail' => ,
            'sinister_id' => ,
        ];
    }
}

