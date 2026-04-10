<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Blueprint;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Entry>
 */
final class EntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'blueprint_id' => Blueprint::factory(),
            'title' => mb_rtrim($title, '.'),
            'slug' => str($title)->slug(),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
            'author_id' => User::factory(),
            'published_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-1 year') : null,
            'layout' => [],
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
}
