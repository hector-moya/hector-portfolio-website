<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EntryElement>
 */
class EntryElementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $handle = fake()->word();

        return [
            'entry_id' => \App\Models\Entry::factory(),
            'blueprint_element_id' => \App\Models\BlueprintElement::factory(),
            'handle' => $handle,
            'value' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
