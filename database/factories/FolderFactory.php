<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Folder>
 */
class FolderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $path = '/'.fake()->word();

        return [
            'name' => trim($path, '/'),
            'path' => $path,
            'parent_id' => null,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
