<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole   = Role::where('name', 'admin')->first();
        $adminAddress = Address::inRandomOrder()->first();

        // Guaranteed admin user for development access
        User::firstOrCreate(
            ['email' => 'admin@dfmsecure.com'],
            [
                'name'             => 'Admin',
                'father_lastname'  => 'DFM',
                'mother_lastname'  => 'Secure',
                'username'         => 'admin',
                'email'            => 'admin@dfmsecure.com',
                'password'         => Hash::make('password'),
                'phone'            => '5500000001',
                'birth_date'       => '1990-01-01',
                'gender'           => 'male',
                'role_id'          => $adminRole?->id,
                'address_id'       => $adminAddress?->id,
                'email_verified_at' => now(),
            ]
        );

        // Random users: 10 insured, 5 adjusters, 3 supervisors
        User::factory()->insured()->count(10)->create();
        User::factory()->adjuster()->count(5)->create();
        User::factory()->supervisor()->count(3)->create();
    }
}

