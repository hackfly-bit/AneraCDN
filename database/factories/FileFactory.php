<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->word().'.txt';

        return [
            'name' => $name,
            'display_name' => pathinfo($name, PATHINFO_FILENAME),
            'slug' => (string) Str::uuid(),
            'path' => 'files/'.Str::uuid().'.txt',
            'disk' => 'public',
            'mime_type' => 'text/plain',
            'extension' => 'txt',
            'size' => 100,
            'hash' => hash('sha256', Str::uuid()),
            'metadata' => null,
            'is_public' => true,
            'is_optimized' => false,
            'user_id' => User::factory(),
        ];
    }
}
