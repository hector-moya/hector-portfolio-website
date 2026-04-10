<?php

declare(strict_types=1);

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;

test('single-type collection renders entry directly at collection url', function (): void {
    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create([
        'slug' => 'about',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
        'settings' => ['type' => 'single'],
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'About Us',
        'slug' => 'about',
        'status' => 'published',
        'published_at' => now(),
        'layout' => [
            [
                '_id' => 'sec-1',
                'type' => 'hero',
                'data' => [
                    'title' => 'We Build Things',
                    'subtitle' => '',
                    'content' => '',
                    'bg_image' => null,
                    'cta_text' => '',
                    'cta_url' => '',
                    'secondary_cta_text' => '',
                    'secondary_cta_url' => '',
                ],
            ],
        ],
    ]);

    $this->get('/about')
        ->assertOk()
        ->assertSee('We Build Things');
});

test('single-type collection returns 404 for inactive collection', function (): void {
    Blueprint::factory()->create();
    Collection::factory()->create([
        'slug' => 'inactive-page',
        'is_active' => false,
        'settings' => ['type' => 'single'],
    ]);

    $this->get('/inactive-page')->assertNotFound();
});

test('single-type collection with no published entry shows empty state', function (): void {
    $blueprint = Blueprint::factory()->create();
    Collection::factory()->create([
        'slug' => 'empty-page',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
        'settings' => ['type' => 'single'],
    ]);

    $this->get('/empty-page')->assertOk();
});

test('standard collection type still renders listing', function (): void {
    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create([
        'slug' => 'my-blog',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
        'settings' => ['type' => 'standard'],
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'Hello World',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->get('/my-blog')->assertOk()->assertSee('Hello World');
});
