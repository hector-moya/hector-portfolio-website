<?php

namespace Database\Factories;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Term>
 */
class TermFactory extends Factory
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
            'slug' => fn (array $attributes) => \Illuminate\Support\Str::slug($attributes['name']),
            'parent_id' => fn (array $attributes) => $this->faker->boolean(20) ? Term::factory() : null,
        ];
    }
}
