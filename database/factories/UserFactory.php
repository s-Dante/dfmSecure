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

        // Obtenemos una imagen aleatoria. 
        $profilePictures = ['Img_10.jpg', 'Img_3.jpg', 'Img_4.jpg', 'Img_5.jpg', 'Img_6.jpg', 'Img_7.jpg', 'Img_8.jpg', 'Img_9.jpg'];
        $randomImage = fake()->randomElement($profilePictures);
        
        $path = 'database/imgs/profilepictures/' . $randomImage;
        $saveAsBlob = fake()->boolean(40); // 40% de que sea guardada como Blob

        return [
            'name'            => $firstName,
            'father_lastname' => $fatherLastname,
            'mother_lastname' => fake()->boolean(80) ? $motherLastname : null,
            'username'        => $username,
            'profile_picture_url'  => !$saveAsBlob ? $path : null,
            'profile_picture_blob' => $saveAsBlob ? file_get_contents(public_path($path)) : null,
            'email'           => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'        => static::$password ??= Hash::make('password'),
            'phone'           => fake()->phoneNumber(),
            // Cuidado: Si la DB espera un date, el formato 'd-m-Y' (dd-mm-yyyy) dará error en MySQL
            'birth_date'      => fake()->dateTimeBetween('-70 years', '-18 years')->format('d-m-Y'),
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
     * (Estos métodos son útiles en testing o seeders para crear un usuario 
     * ya con un rol en específico sin tener que consultar la BD manualmente)
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
