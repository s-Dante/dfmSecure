<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Address;

use App\Enums\AddressTypeEnum;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types =  AddressTypeEnum::values();
        $type = fake()->randomElement($types);
        $country = $this->faker->country();
        $state = $this->faker->state();
        $city = $this->faker->city();
        $neighborhood = $this->faker->citySuffix();
        $street = $this->faker->streetName();
        $externalNumber = $this->faker->buildingNumber();
        $internalNumber = $this->faker->buildingNumber();
        $zipCode = $this->faker->postcode();

        return [
            'type' => $type,
            'country' => $country,
            'state' => $state,
            'city' => $city,
            'neighborhood' => $neighborhood,
            'street' => $street,
            'external_number' => $externalNumber,
            'internal_number' => $internalNumber,
            'zip_code' => $zipCode
        ];
    }
}
