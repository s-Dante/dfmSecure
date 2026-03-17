<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\InsuredVehicle;
use App\Models\Policy;

class PolicySeeder extends Seeder
{
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

