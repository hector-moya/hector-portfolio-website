<?php

declare(strict_types=1);

use App\Livewire\Assets\EditModal;
use App\Models\Asset;
use App\Models\Entry;
use App\Models\EntryElement;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
});

test('edit modal can be rendered', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(EditModal::class)
        ->assertOk();
});

test('open event loads asset data into form', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $asset = Asset::factory()->create([
        'uploaded_by' => $user->id,
        'title' => 'My Photo',
        'alt_text' => 'A nice photo',
        'caption' => 'Photo caption',
        'description' => 'Internal description',
        'copyright' => '© 2025 Me',
    ]);

    Livewire::actingAs($user)
        ->test(EditModal::class)
        ->dispatch('open-asset-edit', assetId: $asset->id)
        ->assertSet('assetId', $asset->id)
        ->assertSet('title', 'My Photo')
        ->assertSet('alt_text', 'A nice photo')
        ->assertSet('caption', 'Photo caption')
        ->assertSet('description', 'Internal description')
        ->assertSet('copyright', '© 2025 Me');
});

test('can save updated metadata', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $asset = Asset::factory()->create(['uploaded_by' => $user->id]);

    Livewire::actingAs($user)
        ->test(EditModal::class)
        ->dispatch('open-asset-edit', assetId: $asset->id)
        ->set('title', 'Updated Title')
        ->set('alt_text', 'Updated alt')
        ->set('caption', 'Updated caption')
        ->set('copyright', '© 2025')
        ->call('save')
        ->assertDispatched('asset-updated');

    $asset->refresh();
    expect($asset->title)->toBe('Updated Title')
        ->and($asset->alt_text)->toBe('Updated alt')
        ->and($asset->caption)->toBe('Updated caption')
        ->and($asset->copyright)->toBe('© 2025');
});

test('usage count reflects entries referencing the asset', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $asset = Asset::factory()->create(['uploaded_by' => $user->id]);

    // Create an entry element that references the asset
    $entry = Entry::factory()->create();
    EntryElement::factory()->create([
        'entry_id' => $entry->id,
        'value' => (string) $asset->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test(EditModal::class)
        ->dispatch('open-asset-edit', assetId: $asset->id);

    expect($component->get('usageCount'))->toBe(1);
});

test('non-admin cannot update another users asset', function () {
    $owner = User::factory()->create(['role' => 'editor']);
    $other = User::factory()->create(['role' => 'editor']);
    $asset = Asset::factory()->create(['uploaded_by' => $owner->id]);

    Livewire::actingAs($other)
        ->test(EditModal::class)
        ->dispatch('open-asset-edit', assetId: $asset->id)
        ->assertForbidden();
});
