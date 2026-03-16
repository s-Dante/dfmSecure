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
        
        $policy = Policy::inRandomOrder()->first() ?? Policy::factory()->create();

        // Si ya tiene un siniestro, o si es muy antiguo (más de 2 meses), lo cerramos "casi" siempre.
        // Sí es posible (y lógico) tener 2 siniestros abiertos a la vez, pero es raro.
        $hasPreviousSinister = Sinister::where('policy_id', $policy->id)->exists();
        $isOld = $reportDate < now()->subMonths(2);

        if ($hasPreviousSinister || $isOld) {
            $status = fake()->randomElement([SinisterStatusEnum::CLOSED->value, SinisterStatusEnum::REJECTED->value]);
        } else {
            $status = fake()->randomElement(SinisterStatusEnum::values());
        }

        $isClosed   = in_array($status, [SinisterStatusEnum::CLOSED->value, SinisterStatusEnum::REJECTED->value]);

        $adjuster   = User::inRandomOrder()->first() ?? User::factory()->create();
        $supervisor = User::where('id', '!=', $adjuster->id)->inRandomOrder()->first() ?? User::factory()->create();

        return [
            // Cuidado: Si la DB espera un formato date (Y-m-d), el formato 'd-m-Y' dará error en MySQL.
            'occur_date'   => $occurDate->format('d-m-Y'),
            'report_date'  => $reportDate->format('d-m-Y'),
            'close_date'   => $isClosed
                ? fake()->dateTimeBetween($reportDate, 'now')->format('d-m-Y')
                : null,
            'description'  => fake()->randomElement(self::DESCRIPTIONS),
            'location'     => fake()->randomElement(self::LOCATIONS),
            'status'       => $status,
            'adjuster_id'  => $adjuster->id,
            'supervisor_id'=> fake()->boolean(70) ? $supervisor->id : null,
            'policy_id'    => $policy->id,
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

