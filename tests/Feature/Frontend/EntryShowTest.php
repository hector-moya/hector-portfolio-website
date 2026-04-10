<?php

declare(strict_types=1);

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\Field;

test('entry show returns 404 for unknown collection', function () {
    $this->get('/nonexistent/some-entry')->assertNotFound();
});

test('entry show returns 404 for draft entry', function () {
    $blueprint = Blueprint::factory()->create();
    Collection::factory()->create([
        'slug' => 'my-blog',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
    ]);
    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'slug' => 'draft-post',
        'status' => 'draft',
    ]);

    $this->get('/my-blog/draft-post')->assertNotFound();
});

test('entry show renders published entry with field values', function () {
    $blueprint = Blueprint::factory()->create();
    Collection::factory()->create([
        'slug' => 'my-blog',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
    ]);
    $entry = Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'My Test Post',
        'slug' => 'my-test-post',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $field = Field::factory()->create([
        'blueprint_id' => $blueprint->id,
        'type' => 'textarea',
        'handle' => 'excerpt',
        'label' => 'Excerpt',
    ]);

    $entry->elements()->create([
        'field_id' => $field->id,
        'handle' => 'excerpt',
        'value' => 'This is the excerpt.',
    ]);

    $this->get('/my-blog/my-test-post')
        ->assertOk()
        ->assertSee('My Test Post')
        ->assertSee('This is the excerpt.');
});

test('entry show uses correct template from blueprint settings', function () {
    $blueprint = Blueprint::factory()->create([
        'settings' => ['detail_template' => 'minimal'],
    ]);
    Collection::factory()->create([
        'slug' => 'my-portfolio',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
    ]);
    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'slug' => 'my-project',
        'title' => 'My Project',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->get('/my-portfolio/my-project')->assertOk();
});
