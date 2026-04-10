<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Blueprint;
use App\Models\Field;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Field>
 */
final class FieldFactory extends Factory
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
            'blueprint_id' => Blueprint::factory(),
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
