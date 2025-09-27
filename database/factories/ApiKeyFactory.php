<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApiKey>
 */
class ApiKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->words(3, true),
            'key' => bin2hex(random_bytes(32)),
            'last_used_at' => $this->faker->optional()->dateTimeBetween('-30 days'),
            'expires_at' => $this->faker->optional()->dateTimeBetween('+30 days', '+1 year'),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
