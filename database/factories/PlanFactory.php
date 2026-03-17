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

    private const PLANS = [
        [
            'name' => 'Básico',
            'price' => '10000',
            'info' => [
                'coverage' => 'Responsabilidad civil',
                'medical_limit' => 100000,
                'material_limit' => 50000,
                'deductible' => 10,
                'benefits' => ['Asistencia vial', 'Robo total'],
            ],
        ],
        [
            'name' => 'Amplio',
            'price' => '20000',
            'info' => [
                'coverage' => 'Cobertura amplia',
                'medical_limit' => 200000,
                'material_limit' => 100000,
                'deductible' => 5,
                'benefits' => ['Asistencia vial', 'Robo total', 'Daños materiales', 'Gastos medicos'],
            ],
        ],
        [
            'name' => 'Total',
            'price' => '30000',
            'info' => [
                'coverage' => 'Cobertura total',
                'medical_limit' => 300000,
                'material_limit' => 200000,
                'deductible' => 0,
                'benefits' => [
                    'Asistencia vial',
                    'Robo total',
                    'Robo parcial',
                    'Daños materiales',
                    'Gastos medicos amplificados',
                    'Auto sustituto',
                    'Defensa juridica',
                ],
            ],
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $plan = fake()->randomElement(self::PLANS);

        return [
            'name' => $plan['name'],
            'status' => PlanStatusEnum::ACTIVE->value,
            'info' => $plan['info'],
            'price' => $plan['price'],
        ];
    }

    public function basic(): static
    {
        $plan = self::PLANS[0];
        return $this->state(fn() => [
            'name' => $plan['name'],
            'price' => $plan['price'],
            'info' => $plan['info'],
        ]);
    }

    public function amplio(): static
    {
        $plan = self::PLANS[1];
        return $this->state(fn() => [
            'name' => $plan['name'],
            'price' => $plan['price'],
            'info' => $plan['info'],
        ]);
    }

    public function premium(): static
    {
        $plan = self::PLANS[2];
        return $this->state(fn() => [
            'name' => $plan['name'],
            'price' => $plan['price'],
            'info' => $plan['info'],
        ]);
    }
}
