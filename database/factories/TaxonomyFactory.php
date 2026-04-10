<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Taxonomy>
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
