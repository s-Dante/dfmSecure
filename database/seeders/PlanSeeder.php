<?php

namespace Database\Seeders;

use App\Enums\PlanStatusEnum;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates canonical insurance plans. Uses firstOrCreate for idempotency.
     */
    public function run(): void
    {
        $plans = [
            [
                'name'   => 'Plan Básico',
                'status' => PlanStatusEnum::ACTIVE->value,
                'price'  => 1299.00,
                'info'   => [
                    'coverage'       => 'Responsabilidad Civil',
                    'medical_limit'  => 100000,
                    'material_limit' => 50000,
                    'deductible_pct' => 10,
                    'benefits'       => ['Asistencia Vial Básica', 'Robo Total'],
                ],
            ],
            [
                'name'   => 'Plan Amplio',
                'status' => PlanStatusEnum::ACTIVE->value,
                'price'  => 3499.00,
                'info'   => [
                    'coverage'       => 'Cobertura Amplia',
                    'medical_limit'  => 300000,
                    'material_limit' => 150000,
                    'deductible_pct' => 5,
                    'benefits'       => ['Asistencia Vial', 'Robo Total', 'Daños Materiales', 'Gastos Médicos'],
                ],
            ],
            [
                'name'   => 'Plan Premium',
                'status' => PlanStatusEnum::ACTIVE->value,
                'price'  => 6999.00,
                'info'   => [
                    'coverage'       => 'Cobertura Total',
                    'medical_limit'  => 1000000,
                    'material_limit' => 500000,
                    'deductible_pct' => 0,
                    'benefits'       => [
                        'Asistencia Vial 24/7', 'Robo Total', 'Robo Parcial',
                        'Daños Materiales', 'Gastos Médicos Ampliados',
                        'Auto Sustituto', 'Defensa Jurídica',
                    ],
                ],
            ],
        ];

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['name' => $plan['name']], $plan);
        }
    }
}

