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
        $vehicles = json_decode(file_get_contents(database_path('data/vehicles.json')), true);

        $type = fake()->randomKey($vehicles);
        $year = fake()->randomKey($vehicles[$type]);
        $brand = fake()->randomKey($vehicles[$type][$year]);
        $subBrand = fake()->randomKey($vehicles[$type][$year][$brand]);
        $version = fake()->randomElement($vehicles[$type][$year][$brand][$subBrand]);

        return [
            'year' => $year,
            'brand' => $brand,
            'sub_brand' => $subBrand,
            'version' => $version,
            'color' => fake()->randomElement(self::COLORS),
        ];
    }
}
