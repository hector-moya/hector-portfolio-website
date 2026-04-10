<?php

declare(strict_types=1);

use App\Livewire\Collections\Create;
use App\Livewire\Collections\Edit;
use App\Models\Collection;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    actingAs(User::factory()->create(['role' => 'admin']));
});

test('collection type defaults to standard', function (): void {
    Livewire::test(Create::class)
        ->assertSet('form.type', 'standard');
});

test('create collection stores type in settings', function (): void {
    Livewire::test(Create::class)
        ->set('form.name', 'About Page')
        ->set('form.type', 'single')
        ->call('save');

    $collection = Collection::query()->where('slug', 'about-page')->first();
    expect($collection->settings['type'])->toBe('single');
});

test('edit collection loads type from settings', function (): void {
    $collection = Collection::factory()->create([
        'settings' => ['type' => 'single'],
    ]);

    Livewire::test(Edit::class, ['collection' => $collection])
        ->assertSet('form.type', 'single');
});

test('update collection persists type change', function (): void {
    $collection = Collection::factory()->create([
        'settings' => ['type' => 'standard'],
    ]);

    Livewire::test(Edit::class, ['collection' => $collection])
        ->set('form.type', 'single')
        ->call('save');

    expect($collection->fresh()->settings['type'])->toBe('single');
});

test('create standard collection omits type from settings', function (): void {
    Livewire::test(Create::class)
        ->set('form.name', 'Blog')
        ->call('save');

    $collection = Collection::query()->where('slug', 'blog')->first();
    expect($collection->settings)->not->toHaveKey('type');
});
