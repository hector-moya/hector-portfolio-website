<?php

declare(strict_types=1);

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;

test('entries api returns only published entries', function (): void {
    $blueprint = Blueprint::factory()->create();
    Collection::factory()->create([
        'slug' => 'blog',
        'blueprint_id' => $blueprint->id,
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'Published Entry',
        'slug' => 'published-entry',
        'status' => 'published',
        'published_at' => now(),
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'Hidden Draft',
        'slug' => 'hidden-draft',
        'status' => 'draft',
    ]);

    $this->getJson('/api/v1/entries')
        ->assertOk()
        ->assertJsonFragment(['title' => 'Published Entry'])
        ->assertJsonMissing(['title' => 'Hidden Draft']);
});

test('entries api rejects an out-of-bounds per_page value', function (): void {
    $this->getJson('/api/v1/entries?per_page=500')
        ->assertStatus(422)
        ->assertJsonValidationErrors('per_page');
});

test('entries api filters by search term', function (): void {
    $blueprint = Blueprint::factory()->create();

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'Solarpunk Cities',
        'slug' => 'solarpunk-cities',
        'status' => 'published',
        'published_at' => now(),
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'Brutalist Towers',
        'slug' => 'brutalist-towers',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->getJson('/api/v1/entries?search=Solarpunk')
        ->assertOk()
        ->assertJsonFragment(['title' => 'Solarpunk Cities'])
        ->assertJsonMissing(['title' => 'Brutalist Towers']);
});

test('entries api show returns 404 for a draft entry', function (): void {
    $blueprint = Blueprint::factory()->create();

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'slug' => 'secret-draft',
        'status' => 'draft',
    ]);

    $this->getJson('/api/v1/entries/secret-draft')->assertNotFound();
});
