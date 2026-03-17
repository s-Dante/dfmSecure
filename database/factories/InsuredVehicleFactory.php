<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\InsuredVehicle;
use App\Models\User;
use App\Models\VehicleModel;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InsuredVehicle>
 */
class InsuredVehicleFactory extends Factory
{
    protected $model = InsuredVehicle::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vin' => $this->generateVIN(),
            'plate' => $this->generatePlate(),
            'vehicle_model_id' => VehicleModel::inRandomOrder()->first()?->id ?? VehicleModel::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }

    private function generateVIN(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $vin = '';
        for ($i = 0; $i < 17; $i++) {
            $vin .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $vin;
    }

    private function generatePlate(): string
    {
        return strtoupper(fake()->lexify('???')) . '-' . fake()->numerify('##') . '-' . fake()->numerify('##');
    }
}

