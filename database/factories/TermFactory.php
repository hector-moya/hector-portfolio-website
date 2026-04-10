<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Term>
 */
final class TermFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'taxonomy_id' => Taxonomy::factory(),
            'name' => $this->faker->words(2, true),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
            'parent_id' => fn (array $attributes) => $this->faker->boolean(20) ? Term::factory() : null,
        ];
    }
}
