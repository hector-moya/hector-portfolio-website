<?php

declare(strict_types=1);

use App\Enums\SectionType;

test('SectionType enum has all 8 cases', function (): void {
    expect(SectionType::cases())->toHaveCount(8);
});

test('each section type has a non-empty label', function (): void {
    foreach (SectionType::cases() as $type) {
        expect($type->label())->toBeString()->not->toBeEmpty();
    }
});

test('each section type has a non-empty icon', function (): void {
    foreach (SectionType::cases() as $type) {
        expect($type->icon())->toBeString()->not->toBeEmpty();
    }
});

test('each section type defaultData returns an array', function (): void {
    foreach (SectionType::cases() as $type) {
        expect($type->defaultData())->toBeArray()->not->toBeEmpty();
    }
});

test('hero defaultData has expected keys', function (): void {
    $data = SectionType::Hero->defaultData();

    expect($data)->toHaveKeys(['title', 'subtitle', 'content', 'bg_image', 'cta_text', 'cta_url', 'secondary_cta_text', 'secondary_cta_url']);
});

test('gallery defaultData has empty images array', function (): void {
    expect(SectionType::Gallery->defaultData()['images'])->toBe([]);
});

test('features defaultData has empty items array', function (): void {
    expect(SectionType::Features->defaultData()['items'])->toBe([]);
});

test('form defaultData has empty fields array', function (): void {
    expect(SectionType::Form->defaultData()['fields'])->toBe([]);
});

test('tryFrom returns correct case for valid value', function (): void {
    expect(SectionType::tryFrom('hero'))->toBe(SectionType::Hero)
        ->and(SectionType::tryFrom('image_text'))->toBe(SectionType::ImageText);
});

test('tryFrom returns null for unknown type', function (): void {
    expect(SectionType::tryFrom('nonexistent'))->toBeNull();
});
