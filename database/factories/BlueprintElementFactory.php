<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlueprintElement>
 */
class BlueprintElementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $label = fake()->words(2, true);
        $types = ['text', 'textarea', 'richtext', 'image', 'select', 'checkbox', 'number', 'date'];

        return [
            'blueprint_id' => \App\Models\Blueprint::factory(),
            'type' => fake()->randomElement($types),
            'label' => ucfirst($label),
            'handle' => str($label)->slug('_'),
            'instructions' => fake()->sentence(),
            'config' => [],
            'is_required' => fake()->boolean(30),
            'order' => 0,
        ];
    }
}
