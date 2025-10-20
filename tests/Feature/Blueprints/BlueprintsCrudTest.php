<?php

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
    $response->assertSeeLivewire(Index::class);
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
    $response = $this->get(route('blueprints.create'));

    $response->assertSuccessful();
    $response->assertSeeLivewire(Create::class);
});

test('can create a blueprint with fields', function () {
    Livewire::test(Create::class)
        ->set('form.name', 'Blog Post')
        ->set('form.slug', 'blog-post')
        ->set('form.description', 'Blueprint for blog posts')
        ->set('form.is_active', true)
        ->set('form.elements.0.type', 'text')
        ->set('form.elements.0.label', 'Title')
        ->set('form.elements.0.handle', 'title')
        ->set('form.elements.0.is_required', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('blueprints.index'));

    $blueprint = Blueprint::where('slug', 'blog-post')->first();
    expect($blueprint)->not->toBeNull();
    expect($blueprint->name)->toBe('Blog Post');
    expect($blueprint->elements)->toHaveCount(1);
    expect($blueprint->elements->first()->label)->toBe('Title');
});

test('validates required fields when creating blueprint', function () {
    Livewire::test(Create::class)
        ->set('form.name', '')
        ->call('save')
        ->assertHasErrors(['form.name']);
});

test('auto-generates slug when creating blueprint', function () {
    Livewire::test(Create::class)
        ->set('form.name', 'Blog Post')
        ->set('form.elements.0.type', 'text')
        ->set('form.elements.0.label', 'Title')
        ->call('save')
        ->assertHasNoErrors();

    expect(Blueprint::where('slug', 'blog-post')->exists())->toBeTrue();
});

test('can view edit blueprint page', function () {
    $blueprint = Blueprint::factory()->create();

    $response = $this->get(route('blueprints.edit', $blueprint));

    $response->assertSuccessful();
    $response->assertSeeLivewire(Edit::class);
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
        ->assertHasNoErrors()
        ->assertRedirect(route('blueprints.index'));

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

test('can add and remove fields dynamically', function () {
    Livewire::test(Create::class)
        ->call('addElement')
        ->assertCount('form.elements', 2)
        ->call('removeElement', 1)
        ->assertCount('form.elements', 1);
});

test('preserves field order when creating blueprint', function () {
    Livewire::test(Create::class)
        ->set('form.name', 'Multi Field Blueprint')
        ->set('form.elements.0.type', 'text')
        ->set('form.elements.0.label', 'First Field')
        ->call('addElement')
        ->set('form.elements.1.type', 'textarea')
        ->set('form.elements.1.label', 'Second Field')
        ->call('save')
        ->assertHasNoErrors();

    $blueprint = Blueprint::where('name', 'Multi Field Blueprint')->first();
    $elements = $blueprint->elements()->ordered()->get();

    expect($elements)->toHaveCount(2);
    expect($elements->first()->label)->toBe('First Field');
    expect($elements->last()->label)->toBe('Second Field');
});
