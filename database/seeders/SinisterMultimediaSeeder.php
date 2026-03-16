<?php

namespace Database\Seeders;

use App\Enums\SinisterMultimediaTypeEnum;
use App\Models\Sinister;
use App\Models\SinisterMultimedia;
use Illuminate\Database\Seeder;

class SinisterMultimediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 1 to 4 multimedia files per sinister.
     * Photos are most common, followed by documents.
     */
    public function run(): void
    {
        // Weighted distribution: PHOTO appears 3 times, DOCUMENT twice, VIDEO & AUDIO once each
        $weightedTypes = [
            SinisterMultimediaTypeEnum::PHOTO->value,
            SinisterMultimediaTypeEnum::PHOTO->value,
            SinisterMultimediaTypeEnum::PHOTO->value,
            SinisterMultimediaTypeEnum::DOCUMENT->value,
            SinisterMultimediaTypeEnum::DOCUMENT->value,
            SinisterMultimediaTypeEnum::VIDEO->value,
            SinisterMultimediaTypeEnum::AUDIO->value,
        ];

        Sinister::all()->each(function (Sinister $sinister) use ($weightedTypes) {
            $count = rand(1, 4);

            for ($i = 0; $i < $count; $i++) {
                SinisterMultimedia::factory()->create([
                    'sinister_id' => $sinister->id,
                    'type'        => $weightedTypes[array_rand($weightedTypes)],
                ]);
            }
        });
    }
}

