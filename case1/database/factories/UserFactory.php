<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),

            'email' => fake()->unique()->safeEmail(),

            'age' => fake()->numberBetween(18, 60),

            'dist_country' => fake()->country(),

            'gender' => fake()->randomElement([
                'male',
                'female',
            ]),

            'role' => 'user',

            'phone_num' => fake()->unique()->numerify('01#########'),

            'email_verified_at' => now(),

            'password' => static::$password ??= Hash::make('password'),

            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Create a tour guide user.
     */
    public function tourGuide(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 't_guide',
        ]);
    }
}