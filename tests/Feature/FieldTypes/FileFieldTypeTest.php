<?php

use App\FieldTypes\FileFieldType;
use App\Models\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

test('file field type has correct name and label', function () {
    $field = new FileFieldType;

    expect($field->name())->toBe('file')
        ->and($field->label())->toBe('File');
});

test('file field type validates asset existence', function () {
    $field = new FileFieldType;
    $field->setHandle('file');

    $rules = $field->rules();

    expect($rules)->toContain('exists:assets,id');
});

test('file field type can hydrate asset', function () {
    $file = UploadedFile::fake()->create('test.pdf');
    $asset = Asset::factory()->create([
        'filename' => $file->hashName(),
        'original_filename' => 'test.pdf',
        'mime_type' => 'application/pdf',
        'disk' => 'public',
        'path' => $file->hashName(),
    ]);

    $field = new FileFieldType;
    $hydrated = $field->hydrate($asset->id);

    expect($hydrated)->toBeInstanceOf(Asset::class)
        ->and($hydrated->id)->toBe($asset->id);
});

test('file field type can dehydrate asset', function () {
    $file = UploadedFile::fake()->create('test.pdf');
    $asset = Asset::factory()->create([
        'filename' => $file->hashName(),
        'original_filename' => 'test.pdf',
        'mime_type' => 'application/pdf',
        'disk' => 'public',
        'path' => $file->hashName(),
    ]);

    $field = new FileFieldType;
    $dehydrated = $field->dehydrate($asset);

    expect($dehydrated)->toBe($asset->id);
});

test('file field type returns null for invalid asset', function () {
    $field = new FileFieldType;

    expect($field->hydrate(null))->toBeNull()
        ->and($field->dehydrate(null))->toBeNull();
});

test('file field type renders correct view', function () {
    $field = new FileFieldType;

    expect($field->view())->toBe('field-types.file');
});
