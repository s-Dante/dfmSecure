<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\VehicleModel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VehicleModel>
 */
class VehicleModelFactory extends Factory
{
    protected $model = VehicleModel::class;

    private const CATALOG = [
        ['brand' => 'Nissan', 'sub_brand' => 'Kicks', 'versions' => ['Advance', 'Premiums', 'Super']],
        ['brand' => 'Nissan', 'sub_brand' => 'Versa', 'versions' => ['Sedan', 'Premiums', 'Super']],
        ['brand' => 'Toyota', 'sub_brand' => 'Corolla', 'versions' => ['Base', 'S', 'SE', 'XSE']],
        ['brand' => 'VolksWagen', 'sub_brand' => 'Jetta', 'versions' => ['TrendLine', 'ComfortLine', 'HighLine']],
        ['brand' => 'VolksWagen', 'sub_brand' => 'Tiguan', 'versions' => ['TrendLine', 'ComfortLine', 'HighLine']],
        ['brand' => 'VolksWagen', 'sub_brand' => 'Taos', 'versions' => ['TrendLine', 'ComfortLine', 'HighLine']],
        ['brand' => 'Honda', 'sub_brand' => 'Civic', 'versions' => ['LX', 'EX', 'EX-T', 'Turbo']],
        ['brand' => 'Honda', 'sub_brand' => 'CR-V', 'versions' => ['LX', 'EX', 'EX-T', 'Turbo']],
    ];

    private const COLORS = [
        'Blanco', 'Negro', 'Rojo', 'Gris', 'Plata', 'Arena', 'Azul Marino',
        'Verde', 'Amarillo', 'Café', 'Naranja', 'Beige', 'Vino', 'Dorado',
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
            'year' => fake()->numberBetween(2000, 2025),
            'brand' => $vehicle['brand'],
            'sub_brand' => $vehicle['sub_brand'],
            'version' => fake()->randomElement($vehicle['versions']),
            'color' => fake()->randomElement(self::COLORS),
        ];
    }
}

