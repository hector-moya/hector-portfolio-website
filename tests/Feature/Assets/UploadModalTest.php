<?php

declare(strict_types=1);

use App\Livewire\Assets\UploadModal;
use App\Models\Asset;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('asset upload modal can be rendered', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(UploadModal::class)
        ->assertOk();
});

test('user can upload an asset', function (): void {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'admin']);
    $file = UploadedFile::fake()->image('test.jpg');

    Livewire::actingAs($user)
        ->test(UploadModal::class)
        ->set('form.uploadedFiles', [$file])
        ->call('uploadAsset')
        ->assertOk();

    $asset = Asset::query()->latest()->first();
    expect($asset)->not->toBeNull()
        ->and($asset->original_filename)->toBe('test.jpg')
        ->and($asset->mime_type)->toBe('image/jpeg')
        ->and($asset->uploaded_by)->toBe($user->id);

    // Verify file was actually stored
    expect(Storage::disk('public')->exists($asset->path))->toBeTrue();
});

test('user can select an asset', function (): void {
    $user = User::factory()->create();
    $asset = Asset::factory()->create([
        'uploaded_by' => $user->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test(UploadModal::class)
        ->call('selectAsset', $asset->id);

    expect($component->get('selectedAsset.id'))->toBe($asset->id);
});

test('user can create folders', function (): void {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'editor']);

    Livewire::actingAs($user)
        ->test(UploadModal::class)
        ->set('folderForm.name', 'Test Folder')
        ->call('createFolder')
        ->assertOk();

    expect(Folder::query()->where('name', 'Test Folder')->exists())->toBeTrue();
});

test('user can navigate folders', function (): void {
    Storage::fake('public');
    $user = User::factory()->create();

    Storage::disk('public')->makeDirectory('test-folder');

    $component = Livewire::actingAs($user)
        ->test(UploadModal::class)
        ->call('navigateToFolder', '/test-folder');

    expect($component->get('currentFolder'))->toBe('/test-folder');
});

test('user can search assets', function (): void {
    $user = User::factory()->create(['role' => 'admin']);

    Livewire::actingAs($user)
        ->test(UploadModal::class)
        ->set('searchQuery', 'test')
        ->assertOk();
});
