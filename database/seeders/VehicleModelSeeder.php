<?php

namespace Database\Seeders;

use App\Models\VehicleModel;
use Illuminate\Database\Seeder;

class VehicleModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates a curated fixed catalog + random models.
     * Uses firstOrCreate to respect the unique(year, brand, sub_brand, version, color) constraint.
     */
    public function run(): void
    {
        $catalog = [
            ['year' => 2022, 'brand' => 'Nissan',     'sub_brand' => 'Versa',   'version' => 'Advance',     'color' => 'Blanco'],
            ['year' => 2023, 'brand' => 'Toyota',      'sub_brand' => 'Corolla', 'version' => 'SE',          'color' => 'Gris'],
            ['year' => 2021, 'brand' => 'Chevrolet',   'sub_brand' => 'Aveo',    'version' => 'LT',          'color' => 'Negro'],
            ['year' => 2024, 'brand' => 'Volkswagen',  'sub_brand' => 'Jetta',   'version' => 'Highline',    'color' => 'Plata'],
            ['year' => 2023, 'brand' => 'Honda',       'sub_brand' => 'Civic',   'version' => 'EX-T',        'color' => 'Azul Marino'],
            ['year' => 2022, 'brand' => 'Kia',         'sub_brand' => 'Rio',     'version' => 'EX',          'color' => 'Rojo'],
            ['year' => 2021, 'brand' => 'Hyundai',     'sub_brand' => 'Elantra', 'version' => 'GLS',         'color' => 'Blanco'],
            ['year' => 2020, 'brand' => 'Ford',        'sub_brand' => 'Mustang', 'version' => 'GT',          'color' => 'Rojo'],
            ['year' => 2023, 'brand' => 'Mazda',       'sub_brand' => 'CX-5',    'version' => 'Grand Touring', 'color' => 'Gris'],
            ['year' => 2022, 'brand' => 'Toyota',      'sub_brand' => 'RAV4',    'version' => 'XLE',         'color' => 'Verde'],
            ['year' => 2024, 'brand' => 'Nissan',      'sub_brand' => 'Kicks',   'version' => 'Exclusive',   'color' => 'Naranja'],
            ['year' => 2023, 'brand' => 'Chevrolet',   'sub_brand' => 'Trax',    'version' => 'Premier',     'color' => 'Vino'],
        ];

        foreach ($catalog as $vehicle) {
            VehicleModel::firstOrCreate($vehicle);
        }

        // Additional random models via factory
        VehicleModel::factory()->count(8)->create();
    }
}

