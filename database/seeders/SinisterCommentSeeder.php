<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\Sinister;
use App\Models\User;
use App\Models\SinisterComment;

class SinisterCommentSeeder extends Seeder
{
    public function run(): void
    {
        Sinister::with(['adjuster', 'supervisor'])->each(function (Sinister $sinister) {
            $count = rand(2, 5);

            $possibleUsers = collect([$sinister->adjuster_id, $sinister->supervisor_id])
                ->filter()
                ->unique();

            for($i = 0; $i < $count; $i++) {
                SinisterComment::factory()->create([
                    'sinister_id' => $sinister->id,
                    'user_id' => $possibleUsers->isNotEmpty()
                        ? $possibleUsers->random()
                        : User::inRandomOrder()->first()?->id,
                ]);
            }
        });
    }
}

