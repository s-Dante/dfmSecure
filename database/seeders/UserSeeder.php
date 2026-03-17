<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;
use App\Models\Address;
use App\Enums\RoleEnum;
use App\Enums\GenderEnum;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', RoleEnum::ADMIN->value)->first();
        $adminAddress = Address::inRandomOrder()->first();
        $gender = GenderEnum::OTHER->value;

        User::firstOrCreate(
            ['email' => 'admin@dfmsecure.com'],
            [
                'name' => 'Admin',
                'father_lastname' => 'DFM',
                'mother_lastname' => 'Secure',
                'username' => 'dfmSecureAdmin',
                'email' => 'admin@dfmsecure.com',
                'password' => Hash::make('password'),
                'phone' => '528181818181',
                'birth_date' => '2000-01-01',
                'gender' => $gender,
                'role_id' => $adminRole?->id,
                'address_id' => $adminAddress?->id,
                'email_verified_at' => now(),
            ]
        );


        User::factory()->admin()->count(1)->create();
        User::factory()->supervisor()->count(5)->create();
        User::factory()->adjuster()->count(5)->create();
        User::factory()->insured()->count(5)->create();

    }
}

