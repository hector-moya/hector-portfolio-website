<?php

use App\Models\Entry;
use App\Models\Field;

test('home page returns 200 with no entry', function () {
    $this->get('/')->assertStatus(200);
});

test('home page renders hero section title from layout', function () {
    Entry::factory()->create([
        'slug' => 'home',
        'status' => 'published',
        'layout' => [
            [
                '_id' => '550e8400-e29b-41d4-a716-446655440000',
                'type' => 'hero',
                'data' => ['title' => 'Welcome to My Portfolio', 'subtitle' => '', 'content' => '', 'bg_image' => null, 'cta_text' => '', 'cta_url' => '', 'secondary_cta_text' => '', 'secondary_cta_url' => ''],
            ],
        ],
    ]);

    $this->get('/')->assertSee('Welcome to My Portfolio');
});

test('home page renders multiple section types', function () {
    Entry::factory()->create([
        'slug' => 'home',
        'status' => 'published',
        'layout' => [
            [
                '_id' => 'sec-1',
                'type' => 'hero',
                'data' => ['title' => 'Hero Title', 'subtitle' => '', 'content' => '', 'bg_image' => null, 'cta_text' => '', 'cta_url' => '', 'secondary_cta_text' => '', 'secondary_cta_url' => ''],
            ],
            [
                '_id' => 'sec-2',
                'type' => 'cta',
                'data' => ['title' => 'CTA Title', 'content' => '', 'cta_text' => 'Get Started', 'cta_url' => '/contact'],
            ],
            [
                '_id' => 'sec-3',
                'type' => 'features',
                'data' => ['title' => 'Our Features', 'items' => []],
            ],
        ],
    ]);

    $this->get('/')
        ->assertSee('Hero Title')
        ->assertSee('CTA Title')
        ->assertSee('Our Features');
});

test('home page renders empty state when no entry exists', function () {
    $this->get('/')->assertSee('Welcome');
});

test('home page uses legacy element rendering when entry has no layout', function () {
    $entry = Entry::factory()->create([
        'slug' => 'home',
        'status' => 'published',
        'layout' => [],
    ]);

    $entry->elements()->create([
        'field_id' => Field::factory()->create()->id,
        'handle' => 'hero_title',
        'value' => 'Legacy Hero Title',
    ]);

    $this->get('/')->assertSee('Legacy Hero Title');
});

test('draft home entry does not render sections', function () {
    Entry::factory()->create([
        'slug' => 'home',
        'status' => 'draft',
        'layout' => [
            ['_id' => 'sec-1', 'type' => 'hero', 'data' => ['title' => 'Draft Hero', 'subtitle' => '', 'content' => '', 'bg_image' => null, 'cta_text' => '', 'cta_url' => '', 'secondary_cta_text' => '', 'secondary_cta_url' => '']],
        ],
    ]);

    $this->get('/')->assertDontSee('Draft Hero');
});
