<?php

use App\Models\Blueprint;
use App\Models\BlueprintElement;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->blueprint = Blueprint::factory()->create([
        'name' => 'Blog Post',
        'slug' => 'blog-post',
    ]);

    // Create blueprint elements
    BlueprintElement::create([
        'blueprint_id' => $this->blueprint->id,
        'type' => 'text',
        'label' => 'Subtitle',
        'handle' => 'subtitle',
        'is_required' => false,
        'order' => 1,
    ]);

    BlueprintElement::create([
        'blueprint_id' => $this->blueprint->id,
        'type' => 'textarea',
        'label' => 'Excerpt',
        'handle' => 'excerpt',
        'is_required' => true,
        'order' => 2,
    ]);

    $this->collection = Collection::factory()->create([
        'name' => 'Blog',
        'slug' => 'blog',
        'blueprint_id' => $this->blueprint->id,
    ]);
});

test('entries index page renders successfully', function () {
    actingAs($this->user)
        ->get(route('entries'))
        ->assertSuccessful();
});

test('entries index shows all entries', function () {
    $entries = Entry::factory(3)->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(\App\Livewire\Entries\Index::class)
        ->assertSee($entries[0]->title)
        ->assertSee($entries[1]->title)
        ->assertSee($entries[2]->title);
});

test('entries index can search by title', function () {
    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
        'title' => 'Laravel Tutorial',
    ]);

    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
        'title' => 'PHP Best Practices',
    ]);

    Livewire::actingAs($this->user)
        ->test(\App\Livewire\Entries\Index::class)
        ->set('search', 'Laravel')
        ->assertSee('Laravel Tutorial')
        ->assertDontSee('PHP Best Practices');
});

test('entries index can filter by collection', function () {
    $collection2 = Collection::factory()->create([
        'blueprint_id' => $this->blueprint->id,
    ]);

    $entry1 = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
        'title' => 'Entry 1',
    ]);

    $entry2 = Entry::factory()->create([
        'collection_id' => $collection2->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
        'title' => 'Entry 2',
    ]);

    Livewire::actingAs($this->user)
        ->test(\App\Livewire\Entries\Index::class)
        ->set('collectionFilter', $this->collection->id)
        ->assertSee('Entry 1')
        ->assertDontSee('Entry 2');
});

test('entries index can filter by status', function () {
    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
        'status' => 'draft',
        'title' => 'Draft Entry',
    ]);

    Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
        'status' => 'published',
        'title' => 'Published Entry',
    ]);

    Livewire::actingAs($this->user)
        ->test(\App\Livewire\Entries\Index::class)
        ->set('statusFilter', 'draft')
        ->assertSee('Draft Entry')
        ->assertDontSee('Published Entry');
});

test('entries create page renders successfully', function () {
    actingAs($this->user)
        ->get(route('entries.create'))
        ->assertSuccessful();
});

test('can create an entry with dynamic fields', function () {
    Livewire::actingAs($this->user)
        ->test(\App\Livewire\Entries\Create::class)
        ->set('selectedCollectionId', $this->collection->id)
        ->set('form.title', 'My First Blog Post')
        ->set('form.slug', 'my-first-blog-post')
        ->set('form.status', 'draft')
        ->set('form.fieldValues.subtitle', 'An amazing subtitle')
        ->set('form.fieldValues.excerpt', 'This is the excerpt text')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('entries'));

    assertDatabaseHas('entries', [
        'title' => 'My First Blog Post',
        'slug' => 'my-first-blog-post',
        'status' => 'draft',
        'collection_id' => $this->collection->id,
        'author_id' => $this->user->id,
    ]);

    $entry = Entry::where('slug', 'my-first-blog-post')->first();

    assertDatabaseHas('entry_elements', [
        'entry_id' => $entry->id,
        'handle' => 'subtitle',
        'value' => 'An amazing subtitle',
    ]);

    assertDatabaseHas('entry_elements', [
        'entry_id' => $entry->id,
        'handle' => 'excerpt',
        'value' => 'This is the excerpt text',
    ]);
});

test('entry title is required', function () {
    Livewire::actingAs($this->user)
        ->test(\App\Livewire\Entries\Create::class)
        ->set('selectedCollectionId', $this->collection->id)
        ->set('form.title', '')
        ->set('form.slug', 'test-slug')
        ->set('form.fieldValues.excerpt', 'Excerpt')
        ->call('save')
        ->assertHasErrors(['form.title' => 'required']);
});

test('entry slug is required', function () {
    Livewire::actingAs($this->user)
        ->test(\App\Livewire\Entries\Create::class)
        ->set('selectedCollectionId', $this->collection->id)
        ->set('form.title', 'Test Title')
        ->set('form.slug', '')
        ->set('form.fieldValues.excerpt', 'Excerpt')
        ->call('save')
        ->assertHasErrors(['form.slug' => 'required']);
});

test('required blueprint fields are validated', function () {
    Livewire::actingAs($this->user)
        ->test(\App\Livewire\Entries\Create::class)
        ->set('selectedCollectionId', $this->collection->id)
        ->set('form.title', 'Test Title')
        ->set('form.slug', 'test-slug')
        ->set('form.fieldValues.excerpt', '') // Required field
        ->call('save')
        ->assertHasErrors(['form.fieldValues.excerpt' => 'required']);
});

test('entries edit page renders successfully', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
    ]);

    actingAs($this->user)
        ->get(route('entries.edit', $entry))
        ->assertSuccessful();
});

test('can update an entry', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
        'title' => 'Original Title',
        'slug' => 'original-slug',
    ]);

    Livewire::actingAs($this->user)
        ->test(\App\Livewire\Entries\Edit::class, ['entry' => $entry])
        ->set('form.title', 'Updated Title')
        ->set('form.slug', 'updated-slug')
        ->set('form.fieldValues.excerpt', 'Updated excerpt')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('entries'));

    assertDatabaseHas('entries', [
        'id' => $entry->id,
        'title' => 'Updated Title',
        'slug' => 'updated-slug',
    ]);
});

test('can update entry field values', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
    ]);

    // Create initial elements
    $entry->elements()->create([
        'blueprint_element_id' => $this->blueprint->elements->first()->id,
        'handle' => 'subtitle',
        'value' => 'Original Subtitle',
    ]);

    Livewire::actingAs($this->user)
        ->test(\App\Livewire\Entries\Edit::class, ['entry' => $entry])
        ->set('form.fieldValues.subtitle', 'Updated Subtitle')
        ->set('form.fieldValues.excerpt', 'New Excerpt')
        ->call('save')
        ->assertHasNoErrors();

    assertDatabaseHas('entry_elements', [
        'entry_id' => $entry->id,
        'handle' => 'subtitle',
        'value' => 'Updated Subtitle',
    ]);

    assertDatabaseHas('entry_elements', [
        'entry_id' => $entry->id,
        'handle' => 'excerpt',
        'value' => 'New Excerpt',
    ]);
});

test('can delete an entry', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
    ]);

    Livewire::actingAs($this->user)
        ->test(\App\Livewire\Entries\Index::class)
        ->call('delete', $entry->id);

    assertSoftDeleted('entries', ['id' => $entry->id]);
});

test('entry status can be changed to published', function () {
    $entry = Entry::factory()->create([
        'collection_id' => $this->collection->id,
        'blueprint_id' => $this->blueprint->id,
        'author_id' => $this->user->id,
        'status' => 'draft',
    ]);

    // Create entry elements for required fields
    $entry->elements()->create([
        'blueprint_element_id' => $this->blueprint->elements->where('handle', 'excerpt')->first()->id,
        'handle' => 'excerpt',
        'value' => 'Test excerpt content',
    ]);

    Livewire::actingAs($this->user)
        ->test(\App\Livewire\Entries\Edit::class, ['entry' => $entry])
        ->set('form.status', 'published')
        ->call('save')
        ->assertHasNoErrors();

    assertDatabaseHas('entries', [
        'id' => $entry->id,
        'status' => 'published',
    ]);
});
