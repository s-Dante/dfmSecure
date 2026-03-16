<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Policy;
use App\Models\Role;
use App\Models\Sinister;
use App\Models\User;
use Illuminate\Database\Seeder;

class SinisterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 0 to 2 sinisters per policy.
     * Always assigns a user with the adjuster role.
     */
    public function run(): void
    {
        $adjusterRole    = Role::where('name', RoleEnum::ADJUSTER->value)->first();
        $supervisorRole  = Role::where('name', RoleEnum::SUPERVISOR->value)->first();

        $adjusters   = $adjusterRole   ? User::where('role_id', $adjusterRole->id)->pluck('id') : collect();
        $supervisors = $supervisorRole  ? User::where('role_id', $supervisorRole->id)->pluck('id') : collect();

        Policy::all()->each(function (Policy $policy) use ($adjusters, $supervisors) {
            // 70% of policies will have at least 1 sinister
            if (rand(1, 10) > 3) {
                $count = rand(1, 2);
                Sinister::factory()
                    ->count($count)
                    ->create([
                        'policy_id'    => $policy->id,
                        'adjuster_id'  => $adjusters->isNotEmpty()
                            ? $adjusters->random()
                            : User::inRandomOrder()->first()?->id,
                        'supervisor_id' => ($supervisors->isNotEmpty() && rand(1, 10) > 4)
                            ? $supervisors->random()
                            : null,
                    ]);
            }
        });
    }
}

