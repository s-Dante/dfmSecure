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
        /* 
         * Si quieres añadir datos específicos de México y usar random, puedes descomentar lo siguiente:
         * 
         * $mexicanStates = ['Aguascalientes', 'Baja California', '...'];
         * $neighborhoods = ['Centro', 'Del Valle', 'Polanco', '...'];
         * 
         * En `zip_pcode` (código postal), se usó `str_pad` porque en México los códigos postales 
         * son de 5 dígitos fijos, y si faker devuleve '345', se rellenaba con ceros a la izquierda 
         * para que quedara '00345' y así no hubiera problemas en base de datos.
         * 
         * return [
         *     'country'         => 'México',
         *     'state'           => fake()->randomElement($mexicanStates),
         *     'zip_code'        => str_pad((string) fake()->numberBetween(1000, 99999), 5, '0', STR_PAD_LEFT),
         *     ...
         * ];
         */

        return [
            'type'            => fake()->randomElement(AddressTypeEnum::values()),
            'country'         => fake()->country(),
            'state'           => fake()->state(),
            'city'            => fake()->city(),
            'neighborhood'    => fake()->streetSuffix(), // Faker no tiene neighborhood global
            'street'          => fake()->streetName(),
            'external_number' => fake()->buildingNumber(),
            'internal_number' => fake()->boolean(30) ? (string) fake()->numberBetween(1, 50) : null,
            'zip_code'        => fake()->postcode(),
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

