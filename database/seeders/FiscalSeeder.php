<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\Fiscal;
use App\Models\User;
use App\Models\Role;
use App\Enums\RoleEnum;

class FiscalSeeder extends Seeder
{
    public function run(): void
    {
        $insuredRole = Role::where('name', RoleEnum::INSURED->value)->first();

        if (!$insuredRole) {
            $this->command->error('No existe el rol de' . RoleEnum::INSURED->label());
            return;
        }

        $insuredUsers = User::where('role_id', $insuredRole->id)->get();

        foreach ($insuredUsers as $user) {
            if (rand(0, 10) <= 5) {
                Fiscal::factory()->create(['user_id' => $user->id]);
            }
        }
    }
}

