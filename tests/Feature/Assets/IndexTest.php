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
        ->assertSee('test-file.jpg');
});

test('can filter assets by folder', function () {
    $user = User::factory()->create();
    $imagesFolder = \App\Models\Folder::factory()->create(['path' => '/images']);
    $documentsFolder = \App\Models\Folder::factory()->create(['path' => '/documents']);

    $asset1 = Asset::factory()->create(['folder_id' => $imagesFolder->id, 'uploaded_by' => $user->id]);
    $asset2 = Asset::factory()->create(['folder_id' => $documentsFolder->id, 'uploaded_by' => $user->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('openFolder', $imagesFolder->id)
        ->assertSee($asset1->filename)
        ->assertDontSee($asset2->filename);
});

test('can search assets', function () {
    $user = User::factory()->create();
    $asset1 = Asset::factory()->create(['original_filename' => 'findme.jpg', 'uploaded_by' => $user->id]);
    $asset2 = Asset::factory()->create(['original_filename' => 'other.jpg', 'uploaded_by' => $user->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('search', 'findme')
        ->assertSee($asset1->filename)
        ->assertDontSee($asset2->filename);
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

    $rootFolder = \App\Models\Folder::factory()->create(['path' => '/']);
    $documentsFolder = \App\Models\Folder::factory()->create(['path' => '/documents']);

    $asset = Asset::factory()->create([
        'filename' => $file->hashName(),
        'path' => $file->hashName(),
        'folder_id' => $rootFolder->id,
        'disk' => 'public',
        'uploaded_by' => $user->id,
    ]);

    $component = Livewire::actingAs($user)
        ->test(Index::class)
        ->call('openMoveAssetModal', $asset->id);

    // Test the MoveModal component separately since it handles the actual move
    Livewire::test(\App\Livewire\Assets\MoveModal::class, ['selected' => [$asset->id]])
        ->set('folderForm.currentFolderId', $documentsFolder->id)
        ->call('move');

    $asset->refresh();
    expect($asset->folder->path)->toBe('/documents')
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
        ->call('delete', $asset->id);

    expect(Asset::count())->toBe(0);
    expect(Storage::disk('public')->exists($file->hashName()))->toBeFalse();
});
