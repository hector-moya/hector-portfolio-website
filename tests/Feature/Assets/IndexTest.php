<?php

declare(strict_types=1);

use App\Livewire\Assets\Index;
use App\Livewire\Assets\MoveModal;
use App\Models\Asset;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    Storage::fake('public');
});

test('index shows list of assets', function (): void {
    $user = User::factory()->create(['role' => 'admin']);
    Asset::factory()->create([
        'uploaded_by' => $user->id,
        'filename' => 'test-file.jpg',
        'original_filename' => 'test-file.jpg',
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSee('test-file.jpg');
});

test('can filter assets by folder', function (): void {
    $user = User::factory()->create();
    $imagesFolder = Folder::factory()->create(['path' => '/images']);
    $documentsFolder = Folder::factory()->create(['path' => '/documents']);

    $asset1 = Asset::factory()->create(['folder_id' => $imagesFolder->id, 'uploaded_by' => $user->id]);
    $asset2 = Asset::factory()->create(['folder_id' => $documentsFolder->id, 'uploaded_by' => $user->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('openFolder', $imagesFolder->id)
        ->assertSee($asset1->filename)
        ->assertDontSee($asset2->filename);
});

test('can search assets', function (): void {
    $user = User::factory()->create();
    $asset1 = Asset::factory()->create(['original_filename' => 'findme.jpg', 'uploaded_by' => $user->id]);
    $asset2 = Asset::factory()->create(['original_filename' => 'other.jpg', 'uploaded_by' => $user->id]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->set('search', 'findme')
        ->assertSee($asset1->filename)
        ->assertDontSee($asset2->filename);
});

test('can download asset', function (): void {
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

test('can move asset to different folder', function (): void {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->create('test.pdf');
    Storage::disk('public')->putFileAs('/', $file, $file->hashName());

    $rootFolder = Folder::factory()->create(['path' => '/']);
    $documentsFolder = Folder::factory()->create(['path' => '/documents']);

    $asset = Asset::factory()->create([
        'filename' => $file->hashName(),
        'path' => $file->hashName(),
        'folder_id' => $rootFolder->id,
        'disk' => 'public',
        'uploaded_by' => $user->id,
    ]);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('openMoveAssetModal', $asset->id);

    // Test the MoveModal component separately since it handles the actual move
    Livewire::test(MoveModal::class, ['selected' => [$asset->id]])
        ->set('folderForm.currentFolderId', $documentsFolder->id)
        ->call('move');

    $asset->refresh();
    expect($asset->folder->path)->toBe('/documents')
        ->and($asset->path)->toBe('documents/'.$file->hashName());
});

test('moving asset also moves thumbnail and medium variant files', function (): void {
    $user = User::factory()->create();
    $file = UploadedFile::fake()->image('photo.jpg');
    Storage::disk('public')->putFileAs('/', $file, $file->hashName());
    Storage::disk('public')->putFileAs('/thumbs', $file, 'thumb_'.$file->hashName());
    Storage::disk('public')->putFileAs('/thumbs', $file, 'medium_'.$file->hashName());

    $imagesFolder = Folder::factory()->create(['path' => '/images']);

    $asset = Asset::factory()->create([
        'filename' => $file->hashName(),
        'path' => $file->hashName(),
        'disk' => 'public',
        'uploaded_by' => $user->id,
        'meta' => [
            'thumbnail' => 'thumbs/thumb_'.$file->hashName(),
            'medium' => 'thumbs/medium_'.$file->hashName(),
        ],
    ]);

    Livewire::actingAs($user)
        ->test(MoveModal::class, ['selected' => [$asset->id]])
        ->set('folderForm.currentFolderId', $imagesFolder->id)
        ->call('move');

    $asset->refresh();
    expect($asset->path)->toBe('images/'.$file->hashName())
        ->and($asset->meta['thumbnail'])->toBe('images/thumb_'.$file->hashName())
        ->and($asset->meta['medium'])->toBe('images/medium_'.$file->hashName())
        ->and(Storage::disk('public')->exists('images/'.$file->hashName()))->toBeTrue()
        ->and(Storage::disk('public')->exists('images/thumb_'.$file->hashName()))->toBeTrue()
        ->and(Storage::disk('public')->exists('images/medium_'.$file->hashName()))->toBeTrue();
});

test('can delete asset', function (): void {
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

    expect(Asset::query()->count())->toBe(0);
    expect(Storage::disk('public')->exists($file->hashName()))->toBeFalse();
});
