<?php

use App\Livewire\Assets\Index;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Storage::fake('public');
});

test('index shows list of assets', function () {
    $user = User::factory()->create(['role' => 'admin']);
    $asset = Asset::factory()->create([
        'uploaded_by' => $user->id,
        'filename' => 'test-file.jpg',
        'original_filename' => 'test-file.jpg',
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertViewHas('assets')
        ->assertSeeHtml('test-file.jpg');
});

test('can filter assets by folder', function () {
    $user = User::factory()->create();
    $asset1 = Asset::factory()->create(['folder' => '/images', 'uploaded_by' => $user->id]);
    $asset2 = Asset::factory()->create(['folder' => '/documents', 'uploaded_by' => $user->id]);

    $component = Livewire::actingAs($user)
        ->test(Index::class)
        ->set('folder', '/images');

    $assets = $component->viewData('assets');
    expect($assets)->toHaveCount(1)
        ->and($assets[0]->id)->toBe($asset1->id);
});

test('can search assets', function () {
    $user = User::factory()->create();
    $asset1 = Asset::factory()->create(['original_filename' => 'findme.jpg', 'uploaded_by' => $user->id]);
    $asset2 = Asset::factory()->create(['original_filename' => 'other.jpg', 'uploaded_by' => $user->id]);

    $component = Livewire::actingAs($user)
        ->test(Index::class)
        ->set('search', 'findme');

    $assets = $component->viewData('assets');
    expect($assets)->toHaveCount(1)
        ->and($assets[0]->id)->toBe($asset1->id);
});

test('can download asset', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('test.pdf');

    Storage::disk('public')->putFileAs('/', $file, $file->hashName());

    $asset = Asset::factory()->create([
        'filename' => $file->hashName(),
        'original_filename' => 'test.pdf',
        'path' => $file->hashName(),
        'mime_type' => 'application/pdf',
        'disk' => 'public',
        'uploaded_by' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('download', $asset->id)
        ->assertDispatched('download-file');
});

test('can move asset to different folder', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('test.pdf');
    Storage::disk('public')->putFileAs('/', $file, $file->hashName());

    $asset = Asset::factory()->create([
        'filename' => $file->hashName(),
        'path' => $file->hashName(),
        'folder' => '/',
        'disk' => 'public',
        'uploaded_by' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('confirmMove', $asset->id)
        ->assertSet('assetToMove', $asset->id)
        ->assertSet('showMoveModal', true)
        ->set('targetFolder', '/documents')
        ->call('move')
        ->assertDispatched('asset-moved');

    $asset->refresh();
    expect($asset->folder)->toBe('/documents')
        ->and($asset->path)->toBe('documents/'.$file->hashName());
});

test('can delete asset', function () {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('test.pdf');
    Storage::disk('public')->putFileAs('/', $file, $file->hashName());

    $asset = Asset::factory()->create([
        'filename' => $file->hashName(),
        'path' => $file->hashName(),
        'disk' => 'public',
        'uploaded_by' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('confirmDelete', $asset->id)
        ->assertSet('assetToDelete', $asset->id)
        ->assertSet('showDeleteModal', true)
        ->call('delete');

    expect(Asset::count())->toBe(0);
    expect(Storage::disk('public')->exists($file->hashName()))->toBeFalse();
});
