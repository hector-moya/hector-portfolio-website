<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entry>
 */
class EntryFactory extends Factory
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
            'collection_id' => \App\Models\Collection::factory(),
            'blueprint_id' => \App\Models\Blueprint::factory(),
            'title' => rtrim($title, '.'),
            'slug' => str($title)->slug(),
            'status' => fake()->randomElement(['draft', 'published', 'archived']),
            'author_id' => \App\Models\User::factory(),
            'published_at' => fake()->boolean(70) ? fake()->dateTimeBetween('-1 year') : null,
            'layout' => [],
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
}
