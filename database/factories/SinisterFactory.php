<?php

namespace Database\Factories;

use App\Enums\SinisterStatusEnum;
use App\Models\Policy;
use App\Models\Sinister;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sinister>
 */
class SinisterFactory extends Factory
{
    protected $model = Sinister::class;

    private const LOCATIONS = [
        'Av. Insurgentes Sur 1602, CDMX',
        'Blvd. Adolfo López Mateos, Monterrey, NL',
        'Periférico Poniente, Guadalajara, Jal',
        'Av. Revolución 1546, Tijuana, BC',
        'Blvd. Kukulcán Km 12.5, Cancún, QR',
        'Av. Constituyentes 45, Puebla, Pue',
        'Carr. Panamericana Km 30, Chihuahua, Chih',
        'Blvd. Fundadores 123, San Luis Potosí, SLP',
        'Av. Hidalgo 200, Mérida, Yuc',
        'Calle 5 de Mayo 88, Oaxaca, Oax',
    ];

    private const DESCRIPTIONS = [
        'Colisión frontal con otro vehículo en crucero sin semáforo.',
        'Impacto lateral al cambiar de carril en autopista.',
        'Daños por granizo severo. Carrocería y parabrisas afectados.',
        'Robo total del vehículo en estacionamiento público.',
        'Vehículo chocado mientras estaba estacionado. Responsable no identificado.',
        'Colisión trasera en semáforo en rojo por distractores del conductor detrás.',
        'Hidroplaneo en carretera mojada, impacto contra barda.',
        'Incendio parcial del motor por falla mecánica.',
        'Volcadura en curva pronunciada sin señalización.',
        'Daños por inundación. Motor y sistema eléctrico afectados.',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $occurDate  = fake()->dateTimeBetween('-1 year', '-1 week');
        $reportDate = fake()->dateTimeBetween($occurDate, 'now');
        $status     = fake()->randomElement(SinisterStatusEnum::values());
        $isClosed   = in_array($status, [SinisterStatusEnum::CLOSED->value, SinisterStatusEnum::REJECTED->value]);

        $adjuster   = User::inRandomOrder()->first();
        $supervisor = User::where('id', '!=', $adjuster?->id)->inRandomOrder()->first();

        return [
            'occur_date'   => $occurDate->format('Y-m-d'),
            'report_date'  => $reportDate->format('Y-m-d'),
            'close_date'   => $isClosed
                ? fake()->dateTimeBetween($reportDate, 'now')->format('Y-m-d')
                : null,
            'description'  => fake()->randomElement(self::DESCRIPTIONS),
            'location'     => fake()->randomElement(self::LOCATIONS),
            'status'       => $status,
            'adjuster_id'  => $adjuster?->id ?? User::factory(),
            'supervisor_id' => fake()->boolean(70) ? $supervisor?->id : null,
            'policy_id'    => Policy::inRandomOrder()->first()?->id ?? Policy::factory(),
        ];
    }

    /**
     * State for an open/in-review sinister.
     */
    public function inReview(): static
    {
        return $this->state(fn(array $attributes) => [
            'status'     => SinisterStatusEnum::IN_REVIEW->value,
            'close_date' => null,
        ]);
    }

    /**
     * State for a closed sinister.
     */
    public function closed(): static
    {
        return $this->state(function (array $attributes) {
            $closeDate = fake()->dateTimeBetween($attributes['report_date'], 'now');
            return [
                'status'     => SinisterStatusEnum::CLOSED->value,
                'close_date' => $closeDate->format('Y-m-d'),
            ];
        });
    }
}

