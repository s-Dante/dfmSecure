<?php

namespace Database\Factories;

use App\Models\InsuredVehicle;
use App\Models\User;
use App\Models\VehicleModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InsuredVehicle>
 */
class InsuredVehicleFactory extends Factory
{
    protected $model = InsuredVehicle::class;

    /**
     * Generate a random 17-character VIN.
     * Uses uppercase letters and digits, excluding I, O, Q.
     */
    private function generateVin(): string
    {
        $chars = 'ABCDEFGHJKLMNPRSTUVWXYZ0123456789';
        $vin   = '';
        for ($i = 0; $i < 17; $i++) {
            $vin .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $vin;
    }

    /**
     * Generate a Mexican license plate: 3 letters + dash + 3 digits (e.g. ABC-123).
     */
    private function generateMexicanPlate(): string
    {
        return strtoupper(fake()->lexify('???')) . '-' . fake()->numerify('###');
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vin'              => $this->generateVin(),
            'plate'            => $this->generateMexicanPlate(),
            'vehicle_model_id' => VehicleModel::inRandomOrder()->first()?->id ?? VehicleModel::factory(),
            'user_id'          => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}

