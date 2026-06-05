<?php

declare(strict_types=1);

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;

test('sitemap lists published entries and active collections but excludes drafts', function (): void {
    $blueprint = Blueprint::factory()->create();
    Collection::factory()->create([
        'name' => 'Blog',
        'slug' => 'blog',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'slug' => 'published-post',
        'status' => 'published',
        'published_at' => now(),
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'slug' => 'draft-post',
        'status' => 'draft',
    ]);

    $response = $this->get('/sitemap.xml')->assertOk();

    $response->assertHeader('Content-Type', 'application/xml');
    $response->assertSee(url('/'), false);
    $response->assertSee(url('/blog'), false);
    $response->assertSee(url('/blog/published-post'), false);
    $response->assertDontSee('draft-post');
});

test('sitemap excludes the home entry slug', function (): void {
    $blueprint = Blueprint::factory()->create();
    Collection::factory()->create([
        'slug' => 'pages',
        'blueprint_id' => $blueprint->id,
        'is_active' => true,
    ]);

    Entry::factory()->create([
        'blueprint_id' => $blueprint->id,
        'slug' => 'home',
        'status' => 'published',
        'published_at' => now(),
    ]);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertDontSee(url('/pages/home'), false);
});

test('robots.txt references the sitemap', function (): void {
    $this->get('/robots.txt')
        ->assertOk()
        ->assertSee('Sitemap: '.route('sitemap'), false);
});
