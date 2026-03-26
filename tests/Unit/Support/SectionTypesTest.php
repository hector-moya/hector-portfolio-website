<?php

use App\Support\SectionTypes;

test('all returns all 6 section types', function () {
    expect(SectionTypes::all())->toHaveKeys(['hero', 'text', 'image_text', 'gallery', 'cta', 'features']);
});

test('each section type has a label and fields', function () {
    foreach (SectionTypes::all() as $schema) {
        expect($schema)->toHaveKeys(['label', 'fields'])
            ->and($schema['label'])->toBeString()->not->toBeEmpty()
            ->and($schema['fields'])->toBeArray()->not->toBeEmpty();
    }
});

test('get returns the schema for a known type', function () {
    $schema = SectionTypes::get('hero');

    expect($schema)->toBeArray()
        ->and($schema['label'])->toBe('Hero')
        ->and($schema['fields'])->toHaveKey('title');
});

test('get returns null for an unknown type', function () {
    expect(SectionTypes::get('nonexistent'))->toBeNull();
});

test('defaults returns an array with correct keys for each type', function () {
    foreach (array_keys(SectionTypes::all()) as $type) {
        $defaults = SectionTypes::defaults($type);
        $fields = SectionTypes::get($type)['fields'];

        expect($defaults)->toBeArray()->toHaveKeys(array_keys($fields));
    }
});

test('defaults for gallery type returns empty array for images', function () {
    expect(SectionTypes::defaults('gallery')['images'])->toBe([]);
});

test('defaults for features type returns empty array for items', function () {
    expect(SectionTypes::defaults('features')['items'])->toBe([]);
});

test('labels returns associative array of type to display name', function () {
    $labels = SectionTypes::labels();

    expect($labels)
        ->toHaveKey('hero', 'Hero')
        ->toHaveKey('cta', 'Call to Action')
        ->toHaveKey('features', 'Features');
});
