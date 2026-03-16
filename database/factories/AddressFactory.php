<?php

namespace Database\Factories;

use App\Enums\AddressTypeEnum;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mexicanStates = [
            'Aguascalientes', 'Baja California', 'Baja California Sur', 'Campeche',
            'Chiapas', 'Chihuahua', 'Ciudad de México', 'Coahuila', 'Colima',
            'Durango', 'Guanajuato', 'Guerrero', 'Hidalgo', 'Jalisco',
            'Estado de México', 'Michoacán', 'Morelos', 'Nayarit', 'Nuevo León',
            'Oaxaca', 'Puebla', 'Querétaro', 'Quintana Roo', 'San Luis Potosí',
            'Sinaloa', 'Sonora', 'Tabasco', 'Tamaulipas', 'Tlaxcala',
            'Veracruz', 'Yucatán', 'Zacatecas',
        ];

        $neighborhoods = [
            'Centro', 'Del Valle', 'Polanco', 'Condesa', 'Roma Norte', 'Roma Sur',
            'Coyoacán', 'Xochimilco', 'Tepito', 'La Merced', 'Santa Fe',
            'Pedregal', 'Las Lomas', 'Narvarte', 'Doctores', 'Guerrero',
            'Colinas del Bosque', 'Jardines del Bosque', 'San Ángel', 'Peralvillo',
        ];

        return [
            'type'            => fake()->randomElement(AddressTypeEnum::values()),
            'country'         => 'México',
            'state'           => fake()->randomElement($mexicanStates),
            'city'            => fake()->city(),
            'neighborhood'    => fake()->randomElement($neighborhoods),
            'street'          => fake()->streetName(),
            'external_number' => (string) fake()->numberBetween(1, 9999),
            'internal_number' => fake()->boolean(30) ? (string) fake()->numberBetween(1, 50) : null,
            'zip_code'        => str_pad((string) fake()->numberBetween(1000, 99999), 5, '0', STR_PAD_LEFT),
        ];
    }

    /**
     * State for fiscal/billing addresses.
     */
    public function fiscal(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => AddressTypeEnum::FISCAL->value,
        ]);
    }

    /**
     * State for home addresses.
     */
    public function home(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => AddressTypeEnum::HOME->value,
        ]);
    }
}

