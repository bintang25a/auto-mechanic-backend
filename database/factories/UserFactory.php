<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
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
        return [
            'uid' => Str::random(10),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),
            'role' => 'customer',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'photo' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'uid' => 'bintang25a',
            'name' => 'Bintang Al Fizar',
            'email' => 'bintangalfizar25@gmail.com',
            'phone_number' => '082111710709',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'photo' => null,
            'email_verified_at' => now(),
        ]);

    }

    public function staff(): static
    {
        return $this->state(fn (array $attributes) => [
            'uid' => 'alfizar25b',
            'name' => 'Bintang Al Fizar',
            'email' => 'alfizarbintang25@gmail.com',
            'phone_number' => '082111710709',
            'role' => 'staff',
            'password' => Hash::make('staff123'),
            'photo' => null,
            'email_verified_at' => now(),
        ]);
    }

    public function mechanic(): static
    {
        return $this->state(fn (array $attributes) => [
            'uid' => 'bintang37a',
            'name' => 'Bintang Al Fizar',
            'email' => 'bintangalfizar37@gmail.com',
            'phone_number' => '082111710709',
            'role' => 'mechanic',
            'password' => Hash::make('mechanic123'),
            'photo' => null,
            'email_verified_at' => now(),
        ]);
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

    public function mechanical(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'mechanic',
        ]);
    }
}
