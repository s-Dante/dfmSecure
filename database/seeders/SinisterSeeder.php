<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\Sinister;
use App\Models\User;
use App\Models\Role;
use App\Models\Policy;
use App\Enums\RoleEnum;

class SinisterSeeder extends Seeder
{
    public function run(): void
    {
        $adjusterRole = Role::where('name', RoleEnum::ADJUSTER->value)->first();
        $supervisorRole = Role::where('name', RoleEnum::SUPERVISOR->value)->first();

        $adjusters = $adjusterRole ? User::where('role_id', $adjusterRole->id)->pluck('id') : collect();
        $supervisors = $supervisorRole ? User::where('role_id', $supervisorRole->id)->pluck('id') : collect();

        Policy::all()->each(function (Policy $policy) use ($adjusters, $supervisors) {
            if (rand(1, 10) > 3) {
                $count = rand(1, 3);
                Sinister::factory()
                    ->count($count)
                    ->create([
                        'policy_id' => $policy->id,
                        'adjuster_id' => $adjusters->isNotEmpty()
                            ? $adjusters->random()
                            : User::inRandomorder()->first()?->id,
                        'supervisor_id' => ($supervisors->isNotEmpty() && rand(1, 10) > 4)
                            ? $supervisors->random()
                            : null,
                    ]);
            }
        });
    }
}

