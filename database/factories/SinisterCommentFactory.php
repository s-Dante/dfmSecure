<?php

namespace Database\Factories;

use App\Models\Sinister;
use App\Models\SinisterComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SinisterComment>
 */
class SinisterCommentFactory extends Factory
{
    protected $model = SinisterComment::class;

    private const COMMENTS = [
        'Se ha recibido la documentación inicial del siniestro. Pendiente de revisión.',
        'El ajustador visitó el lugar del accidente y tomó evidencia fotográfica.',
        'Se solicitaron documentos adicionales al asegurado (factura del vehículo, licencia).',
        'Peritaje completado. Se determinaron los daños materiales y se enviaron al supervisor.',
        'El asegurado aceptó el monto de reparación propuesto.',
        'Se autorizó el ingreso del vehículo al taller afiliado.',
        'El vehículo fue liberado del taller con las reparaciones completadas.',
        'Se solicita revisión adicional por discrepancias en el informe de daños.',
        'El expediente fue escalado al supervisor para Aprobación final.',
        'Se emitió la liquidación del siniestro. Proceso cerrado.',
        'El asegurado ha presentado una inconformidad con el dictamen.',
        'Se agendó cita con el asegurado para revisión física del vehículo.',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'comment'     => fake()->randomElement(self::COMMENTS),
            'sinister_id' => Sinister::inRandomOrder()->first()?->id ?? Sinister::factory(),
            'user_id'     => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }
}

