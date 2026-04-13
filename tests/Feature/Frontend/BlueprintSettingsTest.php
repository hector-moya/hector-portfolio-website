<?php

declare(strict_types=1);

use App\Models\Blueprint;

test('blueprint can store and read settings', function (): void {
    $blueprint = Blueprint::factory()->create([
        'settings' => ['theme' => 'greenpeace'],
    ]);

    expect($blueprint->fresh()->settings['theme'])->toBe('greenpeace');
});
