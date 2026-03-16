<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Role;
use App\Models\Address;

use App\Enums\GenderEnum;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
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
        $fatherLastname = $this->faker->lastName();
        $motherLastname = $this->faker->lastName();
        $username = fake()->unique()->userName();
        $phone = $this->faker->phoneNumber();
        $birthDate = $this->faker->date();
        $genders = GenderEnum::values();
        $gender = fake()->randomElement($genders);

        $profilePicture = null;
        $hasProfilePicture = $this->faker->boolean(50);
        if($hasProfilePicture){
            $images = [
                'database/imgs/profilepictures/Img_3.jpg',
                'database/imgs/profilepictures/Img_4.jpg',
                'database/imgs/profilepictures/Img_5.jpg',
                'database/imgs/profilepictures/Img_6.jpg',
                'database/imgs/profilepictures/Img_7.jpg',
                'database/imgs/profilepictures/Img_8.jpg',
                'database/imgs/profilepictures/Img_9.jpg',
                'database/imgs/profilepictures/Img_10.jpg'
            ];

            $path = public_path(fake()->randomElement($images));

            $profilePicture = file_get_contents($path);
        }

        $roleId = Role::inRandomOrder()->first()->id;

        $addresId = Address::inRandomOrder()->first()->id;

        return [
            'name' => fake()->name(),
            'father_lastname' => $fatherLastname,
            'mother_lastname' => $motherLastname,
            'username' => $username,
            'profile_picture' => $profilePicture,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone' => $phone,
            'birth_date' => $birthDate,
            'gender' => $gender,
            'role_id' => $roleId,
            'address_id' => $addresId,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
