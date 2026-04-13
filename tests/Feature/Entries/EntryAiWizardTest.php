<?php

declare(strict_types=1);

use App\Ai\Agents\EntryWizardAgent;
use App\Enums\SectionType;
use App\Livewire\Entries\AiWizard;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\Field;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    actingAs(User::factory()->create(['role' => 'admin']));
});

test('entry ai wizard page loads', function (): void {
    $this->get(route('entries.ai-wizard'))->assertOk();
});

test('selecting a collection loads blueprint fields', function (): void {
    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create(['blueprint_id' => $blueprint->id]);

    $tab = $blueprint->tabs()->create(['name' => 'Content', 'handle' => 'content', 'sort_order' => 0]);
    $section = $tab->sections()->create(['name' => 'Main', 'handle' => 'main', 'blueprint_id' => $blueprint->id, 'instructions' => '', 'sort_order' => 0]);
    Field::factory()->create(['blueprint_id' => $blueprint->id, 'section_id' => $section->id, 'type' => 'text', 'label' => 'Title', 'handle' => 'title']);

    Livewire::test(AiWizard::class)
        ->set('collectionId', $collection->id)
        ->assertSet('step', 'describe')
        ->assertCount('blueprintFields', 1);
});

test('generate step calls the AI agent and sets generatedFields', function (): void {
    EntryWizardAgent::fake([[
        'title' => 'My Generated Post',
        'slug' => 'my-generated-post',
        'fields' => ['excerpt' => 'A great post about stuff.', 'content' => '<p>Full content here.</p>'],
    ]]);

    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create(['blueprint_id' => $blueprint->id]);

    Livewire::test(AiWizard::class)
        ->set('collectionId', $collection->id)
        ->set('description', 'A post about Laravel testing best practices')
        ->call('generate')
        ->assertSet('step', 'review')
        ->assertSet('generatedTitle', 'My Generated Post');
});

test('save creates entry with generated field values as draft', function (): void {
    $blueprint = Blueprint::factory()->create();
    $collection = Collection::factory()->create(['blueprint_id' => $blueprint->id]);

    $tab = $blueprint->tabs()->create(['name' => 'Content', 'handle' => 'content', 'sort_order' => 0]);
    $section = $tab->sections()->create(['name' => 'Main', 'handle' => 'main', 'blueprint_id' => $blueprint->id, 'instructions' => '', 'sort_order' => 0]);
    Field::factory()->create([
        'blueprint_id' => $blueprint->id,
        'section_id' => $section->id,
        'type' => SectionType::Text->value,
        'label' => 'Excerpt',
        'handle' => 'excerpt',
    ]);

    EntryWizardAgent::fake([[
        'title' => 'Test Entry',
        'slug' => 'test-entry',
        'fields' => ['excerpt' => ['content' => 'This is the excerpt.', 'alignment' => 'left']],
    ]]);

    Livewire::test(AiWizard::class)
        ->set('collectionId', $collection->id)
        ->set('description', 'A test entry')
        ->call('generate')
        ->call('save')
        ->assertRedirectContains('/entries/');

    $entry = Entry::query()->where('slug', 'test-entry')->first();
    expect($entry)->not->toBeNull();
    expect($entry->status)->toBe('draft');
    expect($entry->elements->firstWhere('handle', 'excerpt')?->meta)->toBe(['content' => 'This is the excerpt.', 'alignment' => 'left']);
});

it('denies access to viewer role users', function (): void {
    $viewer = User::factory()->create(['role' => 'viewer']);

    Livewire::actingAs($viewer)
        ->test(AiWizard::class)
        ->assertForbidden();
});
