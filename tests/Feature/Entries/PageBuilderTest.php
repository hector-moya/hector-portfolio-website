<?php

declare(strict_types=1);

use App\Livewire\Entries\Partials\PageBuilder;
use App\Models\Entry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

test('page builder mounts with empty layout', function (): void {
    $entry = Entry::factory()->create(['layout' => []]);

    Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->assertSet('sections', []);
});

test('page builder mounts with existing layout sections', function (): void {
    $entry = Entry::factory()->create([
        'layout' => [
            ['_id' => 'abc-123', 'type' => 'hero', 'data' => ['title' => 'Welcome Hero', 'subtitle' => '']],
        ],
    ]);

    Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->assertSet('sections.0.type', 'hero')
        ->assertSet('sections.0.data.title', 'Welcome Hero');
});

test('can add a hero section', function (): void {
    $entry = Entry::factory()->create(['layout' => []]);

    Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->set('pendingSectionType', 'hero')
        ->call('addSection')
        ->assertCount('sections', 1)
        ->assertSet('sections.0.type', 'hero');
});

test('add section is ignored for unknown type', function (): void {
    $entry = Entry::factory()->create(['layout' => []]);

    Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->set('pendingSectionType', 'nonexistent')
        ->call('addSection')
        ->assertCount('sections', 0);
});

test('can remove a section', function (): void {
    $entry = Entry::factory()->create([
        'layout' => [
            ['_id' => 'aaa-111', 'type' => 'hero', 'data' => ['title' => 'First']],
            ['_id' => 'bbb-222', 'type' => 'text', 'data' => ['content' => 'Second', 'alignment' => 'left']],
        ],
    ]);

    Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->call('removeSection', 0)
        ->assertCount('sections', 1)
        ->assertSet('sections.0.type', 'text');
});

test('can move a section up', function (): void {
    $entry = Entry::factory()->create([
        'layout' => [
            ['_id' => 'aaa-111', 'type' => 'hero', 'data' => ['title' => 'First']],
            ['_id' => 'bbb-222', 'type' => 'text', 'data' => ['content' => 'Second', 'alignment' => 'left']],
        ],
    ]);

    Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->call('moveSectionUp', 1)
        ->assertSet('sections.0.type', 'text')
        ->assertSet('sections.1.type', 'hero');
});

test('move up does nothing for the first section', function (): void {
    $entry = Entry::factory()->create([
        'layout' => [
            ['_id' => 'aaa-111', 'type' => 'hero', 'data' => ['title' => 'First']],
            ['_id' => 'bbb-222', 'type' => 'text', 'data' => ['content' => 'Second', 'alignment' => 'left']],
        ],
    ]);

    Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->call('moveSectionUp', 0)
        ->assertSet('sections.0.type', 'hero');
});

test('can move a section down', function (): void {
    $entry = Entry::factory()->create([
        'layout' => [
            ['_id' => 'aaa-111', 'type' => 'hero', 'data' => ['title' => 'First']],
            ['_id' => 'bbb-222', 'type' => 'text', 'data' => ['content' => 'Second', 'alignment' => 'left']],
        ],
    ]);

    Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->call('moveSectionDown', 0)
        ->assertSet('sections.0.type', 'text')
        ->assertSet('sections.1.type', 'hero');
});

test('save persists layout to database', function (): void {
    $entry = Entry::factory()->create(['layout' => []]);

    Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->set('pendingSectionType', 'hero')
        ->call('addSection')
        ->set('sections.0.data.title', 'My Hero Title')
        ->call('save');

    assertDatabaseHas('entries', ['id' => $entry->id]);
    expect(Entry::query()->find($entry->id)->layout[0]['data']['title'])->toBe('My Hero Title');
});

test('asset-selected event with section handle updates correct section field', function (): void {
    $sectionId = '550e8400-e29b-41d4-a716-446655440000';
    $entry = Entry::factory()->create([
        'layout' => [
            ['_id' => $sectionId, 'type' => 'hero', 'data' => ['title' => '', 'bg_image' => null]],
        ],
    ]);

    Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->dispatch('asset-selected', handle: sprintf('section_%s_bg_image', $sectionId), value: 42)
        ->assertSet('sections.0.data.bg_image', 42);
});

test('asset-selected event with non-section handle is ignored', function (): void {
    $entry = Entry::factory()->create(['layout' => []]);

    Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->dispatch('asset-selected', handle: 'some_regular_field', value: 99)
        ->assertSet('sections', []);
});

test('can add and remove feature items', function (): void {
    $entry = Entry::factory()->create([
        'layout' => [
            ['_id' => 'feat-111', 'type' => 'features', 'data' => ['title' => 'Features', 'items' => []]],
        ],
    ]);

    $component = Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->call('addFeatureItem', 0)
        ->assertCount('sections.0.data.items', 1)
        ->call('addFeatureItem', 0)
        ->assertCount('sections.0.data.items', 2)
        ->call('removeFeatureItem', 0, 0)
        ->assertCount('sections.0.data.items', 1);
});

test('gallery images can be removed', function (): void {
    $entry = Entry::factory()->create([
        'layout' => [
            ['_id' => 'gal-111', 'type' => 'gallery', 'data' => ['title' => 'Gallery', 'images' => [1, 2, 3]]],
        ],
    ]);

    Livewire::actingAs($this->user)
        ->test(PageBuilder::class, ['entry' => $entry])
        ->call('removeGalleryImage', 0, 1)
        ->assertSet('sections.0.data.images', [1, 3]);
});
