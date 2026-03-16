<?php

namespace Database\Seeders;

use App\Models\Sinister;
use App\Models\SinisterComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class SinisterCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 2 to 5 comments per sinister, assigned to the adjuster or supervisor.
     */
    public function run(): void
    {
        Sinister::with(['adjuster', 'supervisor'])->each(function (Sinister $sinister) {
            $count = rand(2, 5);

            // Collect possible comment authors from users related to sinister
            $possibleUsers = collect([$sinister->adjuster_id, $sinister->supervisor_id])
                ->filter()
                ->unique();

            for ($i = 0; $i < $count; $i++) {
                SinisterComment::factory()->create([
                    'sinister_id' => $sinister->id,
                    'user_id'     => $possibleUsers->isNotEmpty()
                        ? $possibleUsers->random()
                        : User::inRandomOrder()->first()?->id,
                ]);
            }
        });
    }
}

