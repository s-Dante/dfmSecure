<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Plan;
use App\Enums\PlanStatusEnum;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Básico',
                'status' => PlanStatusEnum::ACTIVE->value,
                'price' => '10000.00',
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
                'status' => PlanStatusEnum::ACTIVE->value,
                'price' => '20000.00',
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
                'status' => PlanStatusEnum::ACTIVE->value,
                'price' => '30000.00',
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

        foreach ($plans as $plan) {
            Plan::firstOrCreate(['name' => $plan['name']], $plan);
        }
    }
}
