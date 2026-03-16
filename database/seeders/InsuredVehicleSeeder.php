<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\InsuredVehicle;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class InsuredVehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 1 to 3 vehicles per insured user.
     */
    public function run(): void
    {
        $insuredRole = Role::where('name', RoleEnum::INSURED->value)->first();

        if (!$insuredRole) {
            return;
        }

        User::where('role_id', $insuredRole->id)->each(function (User $user) {
            $count = rand(1, 3);
            InsuredVehicle::factory()
                ->count($count)
                ->create(['user_id' => $user->id]);
        });
    }
}

