<?php

namespace Database\Factories;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'role' => 'viewer',
            'token' => Str::random(64),
            'invited_by' => User::factory()->admin(),
            'expires_at' => now()->addHours(48),
            'accepted_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'expires_at' => now()->subHour(),
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'accepted_at' => now()->subHour(),
        ]);
    }
}
