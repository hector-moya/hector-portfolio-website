<?php

use App\FieldTypes\ImageFieldType;
use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('image field type has correct name and label', function () {
    $field = new ImageFieldType;

    expect($field->name())->toBe('image')
        ->and($field->label())->toBe('Image');
});

test('image field type validates asset existence', function () {
    $field = new ImageFieldType;
    $field->setHandle('image');

    $rules = $field->rules();

    expect($rules)->toContain('exists:assets,id');
});

test('image field type can hydrate asset', function () {
    $file = UploadedFile::fake()->image('test.jpg');
    $asset = Asset::factory()->create([
        'filename' => $file->hashName(),
        'original_filename' => 'test.jpg',
        'mime_type' => 'image/jpeg',
        'disk' => 'public',
        'path' => $file->hashName(),
    ]);

    $field = new ImageFieldType;
    $hydrated = $field->hydrate($asset->id);

    expect($hydrated)->toBeInstanceOf(Asset::class)
        ->and($hydrated->id)->toBe($asset->id);
});

test('image field type can dehydrate asset', function () {
    $file = UploadedFile::fake()->image('test.jpg');
    $asset = Asset::factory()->create([
        'filename' => $file->hashName(),
        'original_filename' => 'test.jpg',
        'mime_type' => 'image/jpeg',
        'disk' => 'public',
        'path' => $file->hashName(),
    ]);

    $field = new ImageFieldType;
    $dehydrated = $field->dehydrate($asset);

    expect($dehydrated)->toBe($asset->id);
});

test('image field type returns null for invalid asset', function () {
    $field = new ImageFieldType;

    expect($field->hydrate(null))->toBeNull()
        ->and($field->dehydrate(null))->toBeNull();
});

test('image field type renders correct view', function () {
    $field = new ImageFieldType;

    expect($field->view())->toBe('field-types.image');
});
