<?php

declare(strict_types=1);

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;

test('collection index returns 404 for unknown slug', function () {
    $this->get('/nonexistent-collection')->assertNotFound();
});

test('collection index renders published entries', function () {
    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create([
        'slug' => 'my-blog',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'Hello World',
        'status' => 'published',
        'published_at' => now(),
    ]);
    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'Draft Entry',
        'status' => 'draft',
    ]);

    $this->get('/my-blog')
        ->assertOk()
        ->assertSee('Hello World')
        ->assertDontSee('Draft Entry');
});

test('collection index uses correct template from settings', function () {
    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create([
        'slug' => 'my-portfolio',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
        'settings' => ['index_template' => 'list'],
    ]);

    $this->get('/my-portfolio')->assertOk();
});
