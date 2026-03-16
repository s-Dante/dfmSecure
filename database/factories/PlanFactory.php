<?php

namespace Database\Factories;

use App\Enums\PlanStatusEnum;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    private const PLANS = [
        [
            'name'  => 'Plan Básico',
            'price' => 1299.00,
            'info'  => [
                'coverage'       => 'Responsabilidad Civil',
                'medical_limit'  => 100000,
                'material_limit' => 50000,
                'deductible'     => 10,
                'benefits'       => ['Asistencia Vial Básica', 'Robo Total'],
            ],
        ],
        [
            'name'  => 'Plan Amplio',
            'price' => 3499.00,
            'info'  => [
                'coverage'       => 'Cobertura Amplia',
                'medical_limit'  => 300000,
                'material_limit' => 150000,
                'deductible'     => 5,
                'benefits'       => ['Asistencia Vial', 'Robo Total', 'Daños Materiales', 'Gastos Médicos'],
            ],
        ],
        [
            'name'  => 'Plan Premium',
            'price' => 6999.00,
            'info'  => [
                'coverage'       => 'Cobertura Total',
                'medical_limit'  => 1000000,
                'material_limit' => 500000,
                'deductible'     => 0,
                'benefits'       => [
                    'Asistencia Vial 24/7', 'Robo Total', 'Robo Parcial',
                    'Daños Materiales', 'Gastos Médicos Ampliados',
                    'Auto Sustituto', 'Defensa Jurídica',
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
            'name'   => $plan['name'] . ' ' . fake()->numberBetween(1, 99),
            'status' => PlanStatusEnum::ACTIVE->value,
            'info'   => $plan['info'],
            'price'  => $plan['price'],
        ];
    }

    /**
     * State for the Basic plan.
     */
    public function basic(): static
    {
        $plan = self::PLANS[0];
        return $this->state(fn() => [
            'name'  => $plan['name'],
            'price' => $plan['price'],
            'info'  => $plan['info'],
        ]);
    }

    /**
     * State for the Amplio plan.
     */
    public function amplio(): static
    {
        $plan = self::PLANS[1];
        return $this->state(fn() => [
            'name'  => $plan['name'],
            'price' => $plan['price'],
            'info'  => $plan['info'],
        ]);
    }

    /**
     * State for the Premium plan.
     */
    public function premium(): static
    {
        $plan = self::PLANS[2];
        return $this->state(fn() => [
            'name'  => $plan['name'],
            'price' => $plan['price'],
            'info'  => $plan['info'],
        ]);
    }
}

