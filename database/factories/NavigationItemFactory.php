<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NavigationItem>
 */
class NavigationItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->words(3, true),
            'url' => '/' . $this->faker->slug(),
            'order' => $this->faker->numberBetween(1, 100),
            'target' => '_self',
            'class' => null,
            'is_active' => true,
        ];
    }
}
