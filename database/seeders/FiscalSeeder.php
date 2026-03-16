<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Fiscal;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class FiscalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates fiscal records for 60% of insured users.
     */
    public function run(): void
    {
        $insuredRole = Role::where('name', RoleEnum::INSURED->value)->first();

        if (!$insuredRole) {
            return;
        }

        $insuredUsers = User::where('role_id', $insuredRole->id)->get();

        foreach ($insuredUsers as $user) {
            // Only about 60% of insured users have fiscal data registered
            if (rand(1, 10) <= 6) {
                Fiscal::factory()->create(['user_id' => $user->id]);
            }
        }
    }
}

