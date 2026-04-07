<?php

namespace Database\Factories;

use App\Models\Entry;
use App\Models\EntryElement;
use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EntryElement>
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
            'entry_id' => Entry::factory(),
            'field_id' => Field::factory(),
            'handle' => $handle,
            'value' => fake()->sentence(),
            'meta' => [],
        ];
    }
}
