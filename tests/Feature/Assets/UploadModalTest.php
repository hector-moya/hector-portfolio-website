<?php

use App\Livewire\Assets\UploadModal;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('asset upload modal can be rendered', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(UploadModal::class)
        ->assertOk();
});

test('user can upload an asset', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('test.jpg');

    Livewire::actingAs($user)
        ->test(UploadModal::class)
        ->set('upload', $file)
        ->call('uploadAsset')
        ->assertOk();

    expect(Storage::disk('public')->exists($file->hashName()))->toBeTrue();

    $asset = Asset::latest()->first();
    expect($asset)->not->toBeNull()
        ->and($asset->filename)->toBe($file->hashName())
        ->and($asset->original_filename)->toBe('test.jpg')
        ->and($asset->mime_type)->toBe('image/jpeg')
        ->and($asset->uploaded_by)->toBe($user->id);
});

test('user can select an asset', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create([
        'uploaded_by' => $user->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test(UploadModal::class)
        ->call('selectAsset', $asset->id);

    expect($component->get('selectedAsset.id'))->toBe($asset->id);
});

test('user can create folders', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(UploadModal::class)
        ->set('newFolderName', 'Test Folder')
        ->call('createFolder');

    expect(Storage::disk('public')->exists('Test-Folder'))->toBeTrue();
});

test('user can navigate folders', function () {
    Storage::fake('public');
    $user = User::factory()->create();

    Storage::disk('public')->makeDirectory('test-folder');

    $component = Livewire::actingAs($user)
        ->test(UploadModal::class)
        ->call('navigateToFolder', '/test-folder');

    expect($component->get('currentFolder'))->toBe('/test-folder');
});

test('user can search assets', function () {
    $user = User::factory()->create();
    $asset1 = Asset::factory()->create([
        'original_filename' => 'test-image.jpg',
        'uploaded_by' => $user->id,
    ]);
    $asset2 = Asset::factory()->create([
        'original_filename' => 'another-file.pdf',
        'uploaded_by' => $user->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test(UploadModal::class)
        ->set('searchQuery', 'test');

    $assets = $component->viewData('assets');
    expect($assets)->toHaveCount(1)
        ->and($assets->first()->id)->toBe($asset1->id);
});
