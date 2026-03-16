<?php

namespace Database\Seeders;

use App\Models\InsuredVehicle;
use App\Models\Policy;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates 1 to 2 policies per insured vehicle.
     */
    public function run(): void
    {
        InsuredVehicle::all()->each(function (InsuredVehicle $vehicle) {
            $count = rand(1, 2);
            Policy::factory()
                ->count($count)
                ->active()
                ->create([
                    'vehicle_id' => $vehicle->id,
                    'insured_id' => $vehicle->user_id,
                ]);
        });
    }
}

