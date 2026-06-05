<?php

declare(strict_types=1);

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;

test('entry page renders SEO meta description and Open Graph tags', function (): void {
    $blueprint = Blueprint::factory()->create();
    Collection::factory()->create([
        'slug' => 'projects',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'My Project',
        'slug' => 'my-project',
        'status' => 'published',
        'published_at' => now(),
        'seo_title' => 'My Project — Case Study',
        'seo_description' => 'A deep dive into a solarpunk design system.',
        'og_image' => 'https://example.com/og.jpg',
    ]);

    $response = $this->get('/projects/my-project')->assertOk();

    $response->assertSee('A deep dive into a solarpunk design system.');
    $response->assertSee('<title>My Project — Case Study</title>', false);
    $response->assertSee('property="og:type" content="article"', false);
    $response->assertSee('property="og:image" content="https://example.com/og.jpg"', false);
    $response->assertSee('name="twitter:card" content="summary_large_image"', false);
    $response->assertSee('rel="canonical"', false);
});

test('entry page falls back to excerpt when seo_description is empty', function (): void {
    $blueprint = Blueprint::factory()->create();
    Collection::factory()->create([
        'slug' => 'notes',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'title' => 'A Note',
        'slug' => 'a-note',
        'status' => 'published',
        'published_at' => now(),
        'seo_description' => null,
        'excerpt' => 'This excerpt becomes the meta description.',
    ]);

    $this->get('/notes/a-note')
        ->assertOk()
        ->assertSee('This excerpt becomes the meta description.');
});
