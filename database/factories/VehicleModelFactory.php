<?php

namespace Database\Factories;

use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleModel>
 */
class VehicleModelFactory extends Factory
{
    protected $model = VehicleModel::class;

    private const CATALOG = [
        ['brand' => 'Nissan',    'sub_brand' => 'Versa',    'versions' => ['Sense', 'Advance', 'Exclusive']],
        ['brand' => 'Nissan',    'sub_brand' => 'Kicks',    'versions' => ['Sense', 'Advance', 'Exclusive']],
        ['brand' => 'Nissan',    'sub_brand' => 'Sentra',   'versions' => ['Sense', 'Advance', 'Exclusive']],
        ['brand' => 'Toyota',    'sub_brand' => 'Corolla',  'versions' => ['Base', 'S', 'XSE', 'SE']],
        ['brand' => 'Toyota',    'sub_brand' => 'Camry',    'versions' => ['LE', 'XSE', 'XLE', 'TRD']],
        ['brand' => 'Toyota',    'sub_brand' => 'RAV4',     'versions' => ['XLE', 'XLE Premium', 'Limited']],
        ['brand' => 'Chevrolet', 'sub_brand' => 'Aveo',     'versions' => ['LS', 'LT', 'Premier']],
        ['brand' => 'Chevrolet', 'sub_brand' => 'Trax',     'versions' => ['LS', 'LT', 'Premier']],
        ['brand' => 'Chevrolet', 'sub_brand' => 'Blazer',   'versions' => ['RS', 'Premier']],
        ['brand' => 'Volkswagen','sub_brand' => 'Jetta',    'versions' => ['Trendline', 'Comfortline', 'Highline']],
        ['brand' => 'Volkswagen','sub_brand' => 'Tiguan',   'versions' => ['Trendline', 'Comfortline', 'Highline']],
        ['brand' => 'Volkswagen','sub_brand' => 'Taos',     'versions' => ['Trendline', 'Comfortline', 'Highline']],
        ['brand' => 'Honda',     'sub_brand' => 'Civic',    'versions' => ['LX', 'EX', 'EX-T', 'Turbo']],
        ['brand' => 'Honda',     'sub_brand' => 'CR-V',     'versions' => ['LX', 'EX', 'EX-L', 'Touring']],
        ['brand' => 'Ford',      'sub_brand' => 'Bronco',   'versions' => ['Base', 'Big Bend', 'Wildtrak', 'Raptor']],
        ['brand' => 'Ford',      'sub_brand' => 'Mustang',  'versions' => ['EcoBoost', 'GT', 'Mach 1', 'Shelby GT500']],
        ['brand' => 'Hyundai',   'sub_brand' => 'Tucson',   'versions' => ['GL', 'GLS', 'Limited', 'N Line']],
        ['brand' => 'Hyundai',   'sub_brand' => 'Elantra',  'versions' => ['GL', 'GLS', 'Limited']],
        ['brand' => 'Kia',       'sub_brand' => 'Sportage', 'versions' => ['LX', 'EX', 'EX Tech', 'SXL']],
        ['brand' => 'Kia',       'sub_brand' => 'Rio',      'versions' => ['LX', 'EX', 'EX Tech']],
        ['brand' => 'Mazda',     'sub_brand' => 'Mazda3',   'versions' => ['Touring', 'Grand Touring', 'Grand Touring Sport']],
        ['brand' => 'Mazda',     'sub_brand' => 'CX-5',     'versions' => ['Touring', 'Grand Touring', 'Grand Touring Sport']],
    ];

    private const COLORS = [
        'Blanco', 'Negro', 'Gris', 'Plata', 'Rojo', 'Azul', 'Azul Marino',
        'Verde', 'Café', 'Beige', 'Naranja', 'Amarillo', 'Vino', 'Dorado',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $vehicle = fake()->randomElement(self::CATALOG);

        return [
            'year'      => fake()->numberBetween(2010, 2025),
            'brand'     => $vehicle['brand'],
            'sub_brand' => $vehicle['sub_brand'],
            'version'   => fake()->randomElement($vehicle['versions']),
            'color'     => fake()->randomElement(self::COLORS),
        ];
    }
}

