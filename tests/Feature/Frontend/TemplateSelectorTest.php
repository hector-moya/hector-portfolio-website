<?php

declare(strict_types=1);

use App\Livewire\Collections\Create as CollectionsCreate;
use App\Livewire\Collections\Edit as CollectionsEdit;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    actingAs(User::factory()->create(['role' => 'admin']));
});

test('collection create saves index_template in settings', function (): void {
    $blueprint = Blueprint::factory()->create();

    Livewire::test(CollectionsCreate::class)
        ->set('form.name', 'Blog')
        ->set('form.blueprint_id', $blueprint->id)
        ->set('form.index_template', 'magazine')
        ->call('save')
        ->assertHasNoErrors();

    expect(Collection::query()->where('name', 'Blog')->first()->settings['index_template'])->toBe('magazine');
});

test('collection edit saves index_template in settings', function (): void {
    $collection = Collection::factory()->create(['settings' => ['index_template' => 'card-grid']]);

    Livewire::test(CollectionsEdit::class, ['collection' => $collection])
        ->set('form.index_template', 'list')
        ->call('save')
        ->assertHasNoErrors();

    expect($collection->fresh()->settings['index_template'])->toBe('list');
});

test('collection edit clears index_template when set to empty', function (): void {
    $collection = Collection::factory()->create(['settings' => ['index_template' => 'magazine']]);

    Livewire::test(CollectionsEdit::class, ['collection' => $collection])
        ->set('form.index_template', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($collection->fresh()->settings['index_template'])->toBeNull();
});
