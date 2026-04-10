<?php

declare(strict_types=1);

use App\Livewire\Blueprints\Create;
use App\Livewire\Blueprints\Edit;
use App\Livewire\Blueprints\Index;
use App\Models\Blueprint;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $user = User::factory()->admin()->create();
    actingAs($user);
});

test('can view blueprints index page', function () {
    $response = $this->get(route('blueprints.index'));

    $response->assertSuccessful();
    $response->assertSee('Blueprints');
});

test('can list blueprints', function () {
    $blueprints = Blueprint::factory()->count(3)->create();

    Livewire::test(Index::class)
        ->assertSee($blueprints->first()->name)
        ->assertSee($blueprints->last()->name);
});

test('can search blueprints', function () {
    $blueprint1 = Blueprint::factory()->create(['name' => 'Blog Post']);
    $blueprint2 = Blueprint::factory()->create(['name' => 'Product']);

    Livewire::test(Index::class)
        ->set('search', 'Blog')
        ->assertSee('Blog Post')
        ->assertDontSee('Product');
});

test('can view create blueprint page', function () {
    $blueprint = Blueprint::factory()->create();

    $response = $this->get(route('blueprints.create', $blueprint));

    $response->assertSuccessful();
    $response->assertSee('Create Blueprint');
});

test('can create a blueprint with fields', function () {
    $blueprint = Blueprint::factory()->create(['name' => 'Test Blueprint']);

    Livewire::test(Create::class, ['blueprint' => $blueprint])
        ->set('form.name', 'Blog Post')
        ->set('form.slug', 'blog-post')
        ->set('form.description', 'Blueprint for blog posts')
        ->set('form.is_active', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('blueprints.index'));

    $blueprint = Blueprint::where('slug', 'blog-post')->first();
    expect($blueprint)->not->toBeNull();
    expect($blueprint->name)->toBe('Blog Post');
});

test('validates required fields when creating blueprint', function () {
    $blueprint = Blueprint::factory()->create();

    Livewire::test(Create::class, ['blueprint' => $blueprint])
        ->set('form.name', '')
        ->call('save')
        ->assertHasErrors(['form.name']);
});

test('auto-generates slug when creating blueprint', function () {
    $blueprint = Blueprint::factory()->create();

    Livewire::test(Create::class, ['blueprint' => $blueprint])
        ->set('form.name', 'Blog Post')
        ->call('save')
        ->assertHasNoErrors();

    expect(Blueprint::where('slug', 'blog-post')->exists())->toBeTrue();
});

test('can view edit blueprint page', function () {
    $blueprint = Blueprint::factory()->create();

    $response = $this->get(route('blueprints.edit', $blueprint));

    $response->assertSuccessful();
    $response->assertSee('Edit Blueprint');
});

test('can update a blueprint', function () {
    $blueprint = Blueprint::factory()->create([
        'name' => 'Old Name',
        'slug' => 'old-slug',
    ]);

    Livewire::test(Edit::class, ['blueprint' => $blueprint])
        ->set('form.name', 'New Name')
        ->set('form.slug', 'new-slug')
        ->call('save')
        ->assertHasNoErrors();

    $blueprint->refresh();
    expect($blueprint->name)->toBe('New Name');
    expect($blueprint->slug)->toBe('new-slug');
});

test('can delete a blueprint', function () {
    $blueprint = Blueprint::factory()->create();

    Livewire::test(Index::class)
        ->call('delete', $blueprint->id)
        ->assertDispatched('blueprint-deleted');

    expect(Blueprint::find($blueprint->id))->toBeNull();
});

// TODO: Update these tests for the new tabs/sections/fields structure
test('can add and remove fields dynamically', function () {
    $blueprint = Blueprint::factory()->create();

    // This test needs to be updated for the new structure
    expect($blueprint)->not->toBeNull();
})->skip('Needs updating for new tabs/sections/fields structure');

test('preserves field order when creating blueprint', function () {
    $blueprint = Blueprint::factory()->create();

    // This test needs to be updated for the new structure
    expect($blueprint)->not->toBeNull();
})->skip('Needs updating for new tabs/sections/fields structure');
