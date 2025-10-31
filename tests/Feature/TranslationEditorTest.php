<?php

use App\Livewire\TranslationEditor;
use App\Models\Blueprint;
use App\Models\Translation;
use Livewire\Livewire;

test('translation editor can be rendered', function () {
    $blueprint = Blueprint::factory()->create([
        'name' => 'Test Blueprint',
    ]);

    Livewire::test(TranslationEditor::class, [
        'model' => $blueprint,
        'field' => 'name',
    ])->assertStatus(200);
});

test('translations can be updated', function () {
    $blueprint = Blueprint::factory()->create([
        'name' => 'Test Blueprint',
    ]);

    Livewire::test(TranslationEditor::class, [
        'model' => $blueprint,
        'field' => 'name',
    ])->call('updateTranslation', 'es', 'Blueprint de Prueba');

    $translation = Translation::where('translatable_id', $blueprint->id)
        ->where('translatable_type', $blueprint::class)
        ->where('locale', 'es')
        ->where('field', 'name')
        ->first();

    expect($translation)->not->toBeNull()
        ->and($translation->value)->toBe('Blueprint de Prueba');
});

test('translations are loaded correctly', function () {
    $blueprint = Blueprint::factory()->create([
        'name' => 'Test Blueprint',
    ]);
    $blueprint->setTranslation('name', 'es', 'Blueprint de Prueba');

    $component = Livewire::test(TranslationEditor::class, [
        'model' => $blueprint,
        'field' => 'name',
    ]);

    expect($component->get('translations'))->toHaveCount(2)
        ->and($component->get('translations'))->toMatchArray([
            'en' => 'Test Blueprint',
            'es' => 'Blueprint de Prueba',
        ]);
});

test('translation changes dispatch event', function () {
    $blueprint = Blueprint::factory()->create([
        'name' => 'Test Blueprint',
    ]);

    Livewire::test(TranslationEditor::class, [
        'model' => $blueprint,
        'field' => 'name',
    ])->call('updateTranslation', 'es', 'Blueprint de Prueba')
        ->assertDispatched('translation-updated');
});
