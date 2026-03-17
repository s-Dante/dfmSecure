<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


use App\Enums\GenderEnum;
use App\Enums\RoleEnum;
use App\Models\Address;
use App\Models\Role;
use App\Models\User;

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
        $firstName = fake()->firstName();
        $fatherLastname = fake()->lastName();
        $motherLastname = fake()->lastName();

        // Generate username: first letter + father lastname, lowercase, no spaces
        $baseUsername = strtolower(substr($firstName, 0, 1) . $fatherLastname);
        $username = Str::slug($baseUsername) . fake()->numberBetween(1, 999);

        $profilePictures = ['Img_10.jpg', 'Img_3.jpg', 'Img_4.jpg', 'Img_5.jpg', 'Img_6.jpg', 'Img_7.jpg', 'Img_8.jpg', 'Img_9.jpg'];
        $randomImage = fake()->randomElement($profilePictures);

        $path = 'database/imgs/profilepictures/' . $randomImage;
        $saveAsBlob = fake()->boolean(40);

        return [
            'name' => $firstName,
            'father_lastname' => $fatherLastname,
            'mother_lastname' => fake()->boolean(80) ? $motherLastname : null,
            'username' => $username,
            'profile_picture_url' => !$saveAsBlob ? $path : null,
            'profile_picture_blob' => $saveAsBlob ? file_get_contents(public_path($path)) : null,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => fake()->phoneNumber(),
            'birth_date' => fake()->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(GenderEnum::values()),
            'role_id' => Role::inRandomOrder()->first()?->id ?? Role::factory(),
            'address_id' => Address::inRandomOrder()->first()?->id ?? Address::factory(),
            'remember_token' => Str::random(10),
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

    public function withRole(string $roleName): static
    {
        return $this->state(function (array $attributes) use ($roleName) {
            $role = Role::where('name', $roleName)->first();
            return ['role_id' => $role?->id];
        });
    }

    public function admin(): static
    {
        return $this->withRole(RoleEnum::ADMIN->value);
    }

    public function supervisor(): static
    {
        return $this->withRole(RoleEnum::SUPERVISOR->value);
    }

    public function adjuster(): static
    {
        return $this->withRole(RoleEnum::ADJUSTER->value);
    }

    public function insured(): static
    {
        return $this->withRole(RoleEnum::INSURED->value);
    }
}
