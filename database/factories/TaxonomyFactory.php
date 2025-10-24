<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Taxonomy>
 */
class TaxonomyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'handle' => $this->faker->unique()->slug(2),
            'name' => $this->faker->words(2, true),
            'hierarchical' => $this->faker->boolean(),
            'single_select' => $this->faker->boolean(),
        ];
    }
}
