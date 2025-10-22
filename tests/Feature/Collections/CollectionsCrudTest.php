<?php

use App\Livewire\Collections\Create;
use App\Livewire\Collections\Edit;
use App\Livewire\Collections\Index;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
    actingAs($this->user);
});

test('can view collections index page', function () {
    $response = $this->get(route('collections.index'));

    $response->assertSuccessful();
    $response->assertSeeLivewire(Index::class);
});

test('can list collections', function () {
    $collections = Collection::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee($collections->first()->name)
        ->assertSee($collections->last()->name);
});

test('can search collections', function () {
    $collections_one = Collection::factory()->create(['name' => 'Blog Posts']);
    $collections_two = Collection::factory()->create(['name' => 'Pages']);

    Livewire::test(Index::class)
        ->set('search', 'Blog')
        ->assertSee('Blog Posts')
        ->assertDontSee('Pages');
});

test('can view create collection page', function () {
    $response = $this->get(route('collections.create'));

    $response->assertSuccessful();
    $response->assertSeeLivewire(Create::class);
});

test('can create a collection', function () {
    $blueprint = Blueprint::factory()->create();

    Livewire::test(Create::class)
        ->set('form.name', 'Blog Posts')
        ->set('form.description', 'A collection of blog articles')
        ->set('form.blueprint_id', $blueprint->id)
        ->set('form.is_active', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('collections.index'));

    expect(Collection::where('name', 'Blog Posts')->exists())->toBeTrue();
});

test('validates required fields when creating collection', function () {
    Livewire::test(Create::class)
        ->set('form.name', '')
        ->call('save')
        ->assertHasErrors(['form.name']);
});

test('auto-generates slug when creating collection', function () {
    Livewire::test(Create::class)
        ->set('form.name', 'Blog Posts')
        ->call('save')
        ->assertHasNoErrors();

    $collection = Collection::where('name', 'Blog Posts')->first();
    expect($collection->slug)->toBe('blog-posts');
});

test('can view edit collection page', function () {
    $collection = Collection::factory()->create();

    $response = $this->get(route('collections.edit', $collection));

    $response->assertSuccessful();
    $response->assertSeeLivewire(Edit::class);
});

test('can update a collection', function () {
    $collection = Collection::factory()->create(['name' => 'Old Name']);
    $blueprint = Blueprint::factory()->create();

    Livewire::test(Edit::class, ['collection' => $collection])
        ->set('form.name', 'New Name')
        ->set('form.description', 'Updated description')
        ->set('form.blueprint_id', $blueprint->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('collections.index'));

    expect($collection->fresh()->name)->toBe('New Name');
});

test('can delete a collection', function () {
    $collection = Collection::factory()->create();

    Livewire::test(Index::class)
        ->call('delete', $collection->id)
        ->assertDispatched('collection-deleted');

    expect(Collection::find($collection->id))->toBeNull();
});
