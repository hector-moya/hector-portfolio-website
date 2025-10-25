<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = fake()->word().'.jpg';

        return [
            'filename' => $filename,
            'original_filename' => $filename,
            'disk' => 'public',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(100000, 5000000),
            'path' => $filename,
            'alt_text' => fake()->sentence(),
            'title' => fake()->words(3, true),
            'folder' => '/',
            'meta' => [],
            'uploaded_by' => \App\Models\User::factory(),
        ];
    }
}
