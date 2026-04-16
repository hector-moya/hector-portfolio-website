<?php

declare(strict_types=1);

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\EntryElement;
use App\Models\Field;

test('entry show returns 404 for unknown collection', function (): void {
    $this->get('/nonexistent/some-entry')->assertNotFound();
});

test('entry show returns 404 for draft entry', function (): void {
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

test('entry show renders published entry with section content', function (): void {
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
        'type' => 'text_block',
        'handle' => 'body',
        'label' => 'Body',
    ]);

    EntryElement::factory()->create([
        'entry_id' => $entry->id,
        'field_id' => $field->id,
        'handle' => 'body',
        'meta' => ['content' => 'This is the excerpt.', 'alignment' => 'left'],
    ]);

    $this->get('/my-blog/my-test-post')
        ->assertOk()
        ->assertSee('This is the excerpt.');
});

test('entry show renders published entry with no sections', function (): void {
    $blueprint = Blueprint::factory()->create();
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
