<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
final class AssetFactory extends Factory
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
            'folder_id' => null,
            'meta' => [],
            'uploaded_by' => User::factory(),
        ];
    }
}
