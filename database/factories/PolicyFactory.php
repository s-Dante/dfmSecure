<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\Policy;
use App\Models\Plan;
use App\Models\User;
use App\Models\InsuredVehicle;
use App\Enums\PolicyStatusEnum;

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
        $endDate = (clone $beginDate)->modify('+1 year');

        $user = User::inRandomOrder()->first() ?? User::factory()->create();
        $vehicle = InsuredVehicle::where('user_id', $user->id)->inRandomOrder()->first()
            ?? InsuredVehicle::factory()->create(['user_id' => $user->id]);

        return [
            'folio' => (string) Str::uuid(),
            'status' => PolicyStatusEnum::ACTIVE->value,
            'begin_validity' => $beginDate,
            'end_validity' => $endDate,
            'vehicle_id' => $vehicle->id,
            'insured_id' => $user->id,
            'plan_id' => Plan::inRandomOrder()->first()?->id ?? Plan::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => PolicyStatusEnum::ACTIVE->value,
            'begin_validity' => now()->subMonths(5)->format('Y-m-d'),
            'end_validity' => now()->addMonths(7)->format('Y-m-d'),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => PolicyStatusEnum::EXPIRED->value,
            'begin_validity' => now()->subYear(2)->format('Y-m-d'),
            'end_validity' => now()->subYear(1)->format('Y-m-d'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => PolicyStatusEnum::PENDING->value,
            'begin_validity' => now()->addDays(5)->format('Y-m-d'),
            'end_validity' => now()->addDays(5)->modify('+1 year')->format('Y-m-d'),
        ]);
    }
}
