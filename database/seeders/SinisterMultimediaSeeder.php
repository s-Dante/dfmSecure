<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\Sinister;
use App\Models\SinisterMultimedia;

class SinisterMultimediaSeeder extends Seeder
{
    public function run(): void
    {
        DB::disableQueryLog();

        $sinisters = Sinister::all();

        if ($sinisters->isEmpty()) {
            $sinisters = Sinister::factory()->count(10)->create();
            $this->command->line('No exisitian siniestros y se crearon', "info");
        }

        foreach ($sinisters as $sinister) {
            $count = rand(1, 4);

            SinisterMultimedia::factory()
                ->count($count)
                ->create([
                    'sinister_id' => $sinister->id,
                ]);
        } 
    }
}
