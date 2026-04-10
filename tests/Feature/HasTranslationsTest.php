<?php

declare(strict_types=1);

use App\Models\Blueprint;

test('models can get and set translations', function (): void {
    $blueprint = Blueprint::factory()->create([
        'name' => 'Test Blueprint',
    ]);

    $blueprint->setTranslation('name', 'es', 'Blueprint de Prueba');

    expect($blueprint->translate('name', 'es'))->toBe('Blueprint de Prueba');
});

test('models fallback to default locale when translation missing', function (): void {
    $blueprint = Blueprint::factory()->create([
        'name' => 'Test Blueprint',
    ]);

    expect($blueprint->translate('name', 'es'))->toBe('Test Blueprint');
});

test('models can get all translations', function (): void {
    $blueprint = Blueprint::factory()->create([
        'name' => 'Test Blueprint',
    ]);

    $blueprint->setTranslation('name', 'es', 'Blueprint de Prueba');
    $blueprint->setTranslation('name', 'fr', 'Blueprint de Test');

    $translations = $blueprint->getTranslations('name');

    expect($translations)->toHaveCount(3)
        ->and($translations)->toMatchArray([
            'en' => 'Test Blueprint',
            'es' => 'Blueprint de Prueba',
            'fr' => 'Blueprint de Test',
        ]);
});
