<?php

namespace Database\Factories;

use App\Enums\GenderEnum;
use App\Models\Address;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName    = fake()->firstName();
        $fatherLastname = fake()->lastName();
        $motherLastname = fake()->lastName();

        // Generate username: first letter + father lastname, lowercase, no spaces
        $baseUsername = strtolower(substr($firstName, 0, 1) . $fatherLastname);
        $username     = Str::slug($baseUsername) . fake()->numberBetween(1, 999);

        return [
            'name'            => $firstName,
            'father_lastname' => $fatherLastname,
            'mother_lastname' => fake()->boolean(80) ? $motherLastname : null,
            'username'        => $username,
            'profile_picture' => null,
            'email'           => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'        => static::$password ??= Hash::make('password'),
            'phone'           => fake()->unique()->numerify('55########'),
            'birth_date'      => fake()->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
            'gender'          => fake()->randomElement(GenderEnum::values()),
            'role_id'         => Role::inRandomOrder()->first()?->id ?? Role::factory(),
            'address_id'      => Address::inRandomOrder()->first()?->id ?? Address::factory(),
            'remember_token'  => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Assign a specific role by name.
     */
    public function withRole(string $roleName): static
    {
        return $this->state(function (array $attributes) use ($roleName) {
            $role = Role::where('name', $roleName)->first();
            return ['role_id' => $role?->id];
        });
    }

    /**
     * State for admin users.
     */
    public function admin(): static
    {
        return $this->withRole('admin');
    }

    /**
     * State for adjuster users.
     */
    public function adjuster(): static
    {
        return $this->withRole('adjuster');
    }

    /**
     * State for supervisor users.
     */
    public function supervisor(): static
    {
        return $this->withRole('supervisor');
    }

    /**
     * State for insured users.
     */
    public function insured(): static
    {
        return $this->withRole('insured');
    }
}
