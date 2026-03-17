<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\InsuredVehicle;
use App\Models\User;
use App\Models\Role;
use App\Enums\RoleEnum;

class InsuredVehicleSeeder extends Seeder
{
    public function run(): void
    {
        $isuredRole = Role::where('name', RoleEnum::INSURED->value)->first();

        if (!$isuredRole) {
            $this->command->error('No existe el rol' . RoleEnum::INSURED->vale);
            return;
        }

        User::where('role_id', $isuredRole->id)->each(function (User $user) {
            $count = rand(1, 4);
            InsuredVehicle::factory()
                ->count($count)
                ->create(['user_id' => $user->id]);
        });
    }
}
