<?php

use App\Models\Blueprint;

test('blueprint can store and read settings', function () {
    $blueprint = Blueprint::factory()->create([
        'settings' => ['detail_template' => 'article'],
    ]);

    expect($blueprint->fresh()->settings['detail_template'])->toBe('article');
});
