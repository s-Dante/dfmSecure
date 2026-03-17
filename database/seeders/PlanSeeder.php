<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Plan;
use App\Enums\PlanStatusEnum;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = json_decode(file_get_contents(database_path('data/plans.json')), true);

        foreach ($plans as $plan) {
            Plan::firstOrCreate(
                ['name' => $plan['name']],
                [
                    'status' => PlanStatusEnum::ACTIVE->value,
                    'price' => $plan['costo']['anual'],
                    'info' => $plan
                ]
            );
        }
    }
}
