<?php

declare(strict_types=1);

use App\Models\Blueprint;
use App\Models\Entry;
use App\Models\EntryElement;
use App\Models\Field;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

// ── Entry::getPageBuilderSections() ─────────────────────────────────────────

test('getPageBuilderSections returns empty array when no elements', function (): void {
    $entry = Entry::factory()->create();
    $entry->load('elements.Field');

    expect($entry->getPageBuilderSections())->toBe([]);
});

test('getPageBuilderSections builds sections from entry elements', function (): void {
    $blueprint = Blueprint::factory()->create();
    $field = Field::factory()->create(['blueprint_id' => $blueprint->id, 'type' => 'hero', 'handle' => 'main_hero']);
    $entry = Entry::factory()->create(['blueprint_id' => $blueprint->id]);

    $element = EntryElement::factory()->create([
        'entry_id' => $entry->id,
        'field_id' => $field->id,
        'handle' => 'main_hero',
        'meta' => ['title' => 'Welcome', 'subtitle' => 'Sub', 'content' => '', 'bg_image' => null, 'cta_text' => '', 'cta_url' => '', 'secondary_cta_text' => '', 'secondary_cta_url' => ''],
    ]);

    $entry->load('elements.Field');
    $sections = $entry->getPageBuilderSections();

    expect($sections)->toHaveCount(1)
        ->and($sections[0]['type'])->toBe('hero')
        ->and($sections[0]['data']['title'])->toBe('Welcome');
});

test('getPageBuilderSections skips elements with unknown field type', function (): void {
    $blueprint = Blueprint::factory()->create();
    $field = Field::factory()->create(['blueprint_id' => $blueprint->id, 'type' => 'unknown_type', 'handle' => 'bad_field']);
    $entry = Entry::factory()->create(['blueprint_id' => $blueprint->id]);

    EntryElement::factory()->create([
        'entry_id' => $entry->id,
        'field_id' => $field->id,
        'handle' => 'bad_field',
        'value' => 'some value',
    ]);

    $entry->load('elements.Field');

    expect($entry->getPageBuilderSections())->toBe([]);
});

test('getPageBuilderSections falls back to defaultData when element value is not an array', function (): void {
    $blueprint = Blueprint::factory()->create();
    $field = Field::factory()->create(['blueprint_id' => $blueprint->id, 'type' => 'text_block', 'handle' => 'body']);
    $entry = Entry::factory()->create(['blueprint_id' => $blueprint->id]);

    EntryElement::factory()->create([
        'entry_id' => $entry->id,
        'field_id' => $field->id,
        'handle' => 'body',
        'value' => 'plain string',
    ]);

    $entry->load('elements.Field');
    $sections = $entry->getPageBuilderSections();

    expect($sections)->toHaveCount(1)
        ->and($sections[0]['type'])->toBe('text_block')
        ->and($sections[0]['data'])->toBe(App\Enums\SectionType::Text->defaultData());
});

// ── Features section component ────────────────────────────────────────────────

test('features section can add an item', function (): void {
    $field = Field::factory()->create(['type' => 'features', 'handle' => 'my_features']);

    Livewire::actingAs($this->user)
        ->test('fields.features', ['field' => $field, 'data' => ['title' => '', 'items' => []]])
        ->call('addFeatureItem')
        ->assertCount('data.items', 1);
});

test('features section can remove an item', function (): void {
    $field = Field::factory()->create(['type' => 'features', 'handle' => 'my_features']);

    Livewire::actingAs($this->user)
        ->test('fields.features', [
            'field' => $field,
            'data' => ['title' => '', 'items' => [
                ['icon' => 'bolt', 'item_title' => 'First', 'item_description' => ''],
                ['icon' => 'star', 'item_title' => 'Second', 'item_description' => ''],
            ]],
        ])
        ->call('removeFeatureItem', 0)
        ->assertCount('data.items', 1)
        ->assertSet('data.items.0.item_title', 'Second');
});

// ── Gallery section component ─────────────────────────────────────────────────

test('gallery section can remove an image', function (): void {
    $field = Field::factory()->create(['type' => 'gallery', 'handle' => 'my_gallery']);

    Livewire::actingAs($this->user)
        ->test('fields.gallery', [
            'field' => $field,
            'data' => ['title' => '', 'images' => [10, 20, 30]],
        ])
        ->call('removeGalleryImage', 1)
        ->assertSet('data.images', [10, 30]);
});

test('gallery section asset-selected appends image', function (): void {
    $field = Field::factory()->create(['type' => 'gallery', 'handle' => 'my_gallery']);

    Livewire::actingAs($this->user)
        ->test('fields.gallery', [
            'field' => $field,
            'data' => ['title' => '', 'images' => []],
        ])
        ->dispatch('asset-selected', handle: 'my_gallery_gallery', value: 99)
        ->assertCount('data.images', 1)
        ->assertSet('data.images.0', 99);
});

// ── Hero section component ────────────────────────────────────────────────────

test('hero section asset-selected updates bg_image', function (): void {
    $field = Field::factory()->create(['type' => 'hero', 'handle' => 'page_hero']);

    Livewire::actingAs($this->user)
        ->test('fields.hero', [
            'field' => $field,
            'data' => App\Enums\SectionType::Hero->defaultData(),
        ])
        ->dispatch('asset-selected', handle: 'page_hero_bg_image', value: 42)
        ->assertSet('data.bg_image', 42);
});

test('hero section asset-selected with wrong handle is ignored', function (): void {
    $field = Field::factory()->create(['type' => 'hero', 'handle' => 'page_hero']);

    Livewire::actingAs($this->user)
        ->test('fields.hero', [
            'field' => $field,
            'data' => App\Enums\SectionType::Hero->defaultData(),
        ])
        ->dispatch('asset-selected', handle: 'other_field_bg_image', value: 42)
        ->assertSet('data.bg_image', null);
});

test('hero section can remove bg image', function (): void {
    $field = Field::factory()->create(['type' => 'hero', 'handle' => 'page_hero']);

    Livewire::actingAs($this->user)
        ->test('fields.hero', [
            'field' => $field,
            'data' => array_merge(App\Enums\SectionType::Hero->defaultData(), ['bg_image' => 5]),
        ])
        ->call('removeBgImage')
        ->assertSet('data.bg_image', null);
});

// ── Form section component ────────────────────────────────────────────────────

test('form section can add a field', function (): void {
    $field = Field::factory()->create(['type' => 'form', 'handle' => 'contact_form']);

    Livewire::actingAs($this->user)
        ->test('fields.form', ['field' => $field, 'data' => ['title' => '', 'fields' => []]])
        ->call('addFormField')
        ->assertCount('data.fields', 1);
});

test('form section can remove a field', function (): void {
    $field = Field::factory()->create(['type' => 'form', 'handle' => 'contact_form']);

    Livewire::actingAs($this->user)
        ->test('fields.form', [
            'field' => $field,
            'data' => ['title' => '', 'fields' => [
                ['type' => 'text', 'label' => 'Name', 'handle' => 'name', 'required' => false],
                ['type' => 'email', 'label' => 'Email', 'handle' => 'email', 'required' => true],
            ]],
        ])
        ->call('removeFormField', 0)
        ->assertCount('data.fields', 1)
        ->assertSet('data.fields.0.handle', 'email');
});
