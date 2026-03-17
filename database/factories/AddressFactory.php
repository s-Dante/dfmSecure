<?php

namespace Database\Factories;

use App\Enums\AddressTypeEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Address;

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
        return [
            'type' => fake()->randomElement(AddressTypeEnum::values()),
            'country' => fake()->country(),
            'state' => fake()->state(),
            'city' => fake()->city(),
            'neighborhood' => fake()->streetSuffix(),
            'street' => fake()->streetName(),
            'external_number' => fake()->buildingNumber(),
            'internal_number' => fake()->boolean(40) ? (string) fake()->buildingNumber() : null,
            'zip_code' => fake()->postcode(),
        ];
    }

    public function fiscal(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => AddressTypeEnum::FISCAL->value,
        ]);
    }

    public function home(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => AddressTypeEnum::HOME->value,
        ]);
    }

    public function office(): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => AddressTypeEnum::OFFICE->value,
        ]);
    }
}
