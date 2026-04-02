<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\Sinister;
use App\Models\Policy;
use App\Models\User;
use App\Models\Role;
use App\Enums\SinisterStatusEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sinister>
 */
class SinisterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $occurDate = fake()->dateTimeBetween('-1 year', '-1 week');
        $reportDate = fake()->dateTimeBetween($occurDate, 'now');

        $policy = Policy::inRandomOrder()->first() ?? Policy::factory()->create();

        $hasPreviousSinister = Sinister::where('policy_id', $policy->id)->exists();
        $isOld = $reportDate < now()->subMonths(2);

        if ($hasPreviousSinister || $isOld) {
            $status = fake()->randomElement([SinisterStatusEnum::CLOSED->value, SinisterStatusEnum::REJECTED->value]);
        } else {
            $status = fake()->randomElement(SinisterStatusEnum::values());
        }

        $isClosed = in_array($status, [SinisterStatusEnum::CLOSED->value, SinisterStatusEnum::REJECTED->value]);

        $adjusterRole = Role::where('name', RoleEnum::ADJUSTER->value)->first();
        $supervisorRole = Role::where('name', RoleEnum::SUPERVISOR->value)->first();

        $adjuster = User::where('role_id', $adjusterRole?->id)->inRandomOrder()->first() ?? User::factory()->create();
        $supervisor = User::where('role_id', $supervisorRole?->id)->inRandomOrder()->first() ?? User::factory()->create();

        return [
            'folio'        => (string) Str::uuid(),
            'occur_date'   => $occurDate->format('Y-m-d'),
            'report_date'  => $reportDate->format('Y-m-d'),
            'close_date'   => $isClosed
                ? fake()->dateTimeBetween($reportDate, 'now')->format('Y-m-d')
                : null,
            'description'  => fake()->text(100),
            'location'     => fake()->address(),
            'status'       => $status,
            'adjuster_id'  => $adjuster->id,
            'supervisor_id' => $supervisor->id,
            'policy_id'    => $policy->id,
        ];
    }

    public function inReview(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => SinisterStatusEnum::IN_REVIEW->value,
            'close_date' => null,
        ]);
    }

    public function closed(): static
    {
        return $this->state(function (array $attributes) {
            $closeDate = fake()->dateTimeBetween($attributes['report_date'], 'now');
            return [
                'status' => SinisterStatusEnum::CLOSED->value,
                'close_date' => $closeDate->format('Y-m-d'),
            ];
        });
    }
}
