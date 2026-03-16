<?php

namespace Database\Factories;

use App\Enums\PolicyStatusEnum;
use App\Models\InsuredVehicle;
use App\Models\Plan;
use App\Models\Policy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Policy>
 */
class PolicyFactory extends Factory
{
    protected $model = Policy::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $beginDate = fake()->dateTimeBetween('-2 years', 'now');
        $endDate   = (clone $beginDate)->modify('+1 year');

        return [
            'folio'          => (string) Str::uuid(),
            'status'         => PolicyStatusEnum::ACTIVE->value,
            'begin_validity' => $beginDate->format('Y-m-d'),
            'end_validity'   => $endDate->format('Y-m-d'),
            'vehicle_id'     => InsuredVehicle::inRandomOrder()->first()?->id ?? InsuredVehicle::factory(),
            'insured_id'     => User::inRandomOrder()->first()?->id ?? User::factory(),
            'plan_id'        => Plan::inRandomOrder()->first()?->id ?? Plan::factory(),
        ];
    }

    /**
     * State for an active policy.
     */
    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'         => PolicyStatusEnum::ACTIVE->value,
            'begin_validity' => now()->subMonths(6)->format('Y-m-d'),
            'end_validity'   => now()->addMonths(6)->format('Y-m-d'),
        ]);
    }

    /**
     * State for an expired policy.
     */
    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'         => PolicyStatusEnum::EXPIRED->value,
            'begin_validity' => now()->subYears(2)->format('Y-m-d'),
            'end_validity'   => now()->subYears(1)->format('Y-m-d'),
        ]);
    }

    /**
     * State for a pending policy.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'         => PolicyStatusEnum::PENDING->value,
            'begin_validity' => now()->addDays(5)->format('Y-m-d'),
            'end_validity'   => now()->addDays(5)->modify('+1 year')->format('Y-m-d'),
        ]);
    }
}

