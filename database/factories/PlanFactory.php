<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Plan;
use App\Enums\PlanStatusEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plans = json_decode(file_get_contents(database_path('data/plans.json')), true);
        $plan = fake()->randomElement($plans);

        return [
            'name' => $plan['name'],
            'status' => PlanStatusEnum::ACTIVE->value,
            'info' => $plan,
            'price' => $plan['costo']['anual'],
        ];
    }

    public function basic(): static
    {
        return $this->state(function (array $attributes) {
            $plans = json_decode(file_get_contents(database_path('data/plans.json')), true);
            $plan = $plans[0];
            return [
                'name' => $plan['name'],
                'price' => $plan['costo']['anual'],
                'info' => $plan,
            ];
        });
    }

    public function amplio(): static
    {
        return $this->state(function (array $attributes) {
            $plans = json_decode(file_get_contents(database_path('data/plans.json')), true);
            $plan = $plans[2];
            return [
                'name' => $plan['name'],
                'price' => $plan['costo']['anual'],
                'info' => $plan,
            ];
        });
    }

    public function premium(): static
    {
        return $this->state(function (array $attributes) {
            $plans = json_decode(file_get_contents(database_path('data/plans.json')), true);
            $plan = $plans[1];
            return [
                'name' => $plan['name'],
                'price' => $plan['costo']['anual'],
                'info' => $plan,
            ];
        });
    }
}
