<?php

declare(strict_types=1);

use App\Livewire\Globals\Create;
use App\Models\Blueprint;
use App\Models\GlobalSet;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    $user = User::factory()->admin()->create();
    actingAs($user);
});

test('can create a global set', function (): void {
    $blueprint = Blueprint::factory()->create();

    Livewire::test(Create::class)
        ->set('form.name', 'Site Settings')
        ->set('form.handle', 'site_settings')
        ->set('form.blueprint_id', $blueprint->id)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect();

    expect(GlobalSet::query()->where('handle', 'site_settings')->exists())->toBeTrue();
});

test('auto-generates handle from name', function (): void {
    Livewire::test(Create::class)
        ->set('form.name', 'Company Information')
        ->assertSet('form.handle', 'company-information');
});

test('validates required fields', function (): void {
    Livewire::test(Create::class)
        ->call('save')
        ->assertHasErrors(['form.name', 'form.handle', 'form.blueprint_id']);
});

test('handle must be unique', function (): void {
    $blueprint = Blueprint::factory()->create();
    GlobalSet::factory()->create(['handle' => 'existing-handle']);

    Livewire::test(Create::class)
        ->set('form.name', 'Test')
        ->set('form.handle', 'existing-handle')
        ->set('form.blueprint_id', $blueprint->id)
        ->call('save')
        ->assertHasErrors(['form.handle']);
});
