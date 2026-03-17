<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

use App\Models\VehicleModel;

class VehicleModelSeeder extends Seeder
{
    public function run(): void
    {
        VehicleModel::factory()->count(10)->create();
    }
}

