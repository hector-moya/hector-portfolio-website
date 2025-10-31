<?php

use App\Livewire\TranslationEditor;
use App\Models\Concerns\HasTranslations;
use App\Models\Entry;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Livewire\Livewire;

class TranslatableModel extends Model
{
    use HasTranslations;

    protected $fillable = ['title'];
    protected $table = 'entries';
}

test('translation editor can be rendered', function () {
    $model = Entry::factory()->create(['title' => 'Test Title']);

    Livewire::test(TranslationEditor::class, [
        'model' => $model,
        'field' => 'title'
    ])->assertStatus(200);
});

test('translations can be updated', function () {
    $model = Entry::factory()->create(['title' => 'Test Title']);

    Livewire::test(TranslationEditor::class, [
        'model' => $model,
        'field' => 'title'
    ])->call('updateTranslation', 'es', 'Título de Prueba');

    $translation = Translation::where('translatable_id', $model->id)
        ->where('locale', 'es')
        ->where('field', 'title')
        ->first();

    expect($translation)->not->toBeNull()
        ->and($translation->value)->toBe('Título de Prueba');
});

test('translations are loaded correctly', function () {
    $model = Entry::factory()->create(['title' => 'Test Title']);
    $model->setTranslation('title', 'es', 'Título de Prueba');

    $component = Livewire::test(TranslationEditor::class, [
        'model' => $model,
        'field' => 'title'
    ]);

    expect($component->get('translations'))->toBe([
        'en' => 'Test Title',
        'es' => 'Título de Prueba'
    ]);
});

test('translation changes dispatch event', function () {
    $model = Entry::factory()->create(['title' => 'Test Title']);

    Livewire::test(TranslationEditor::class, [
        'model' => $model,
        'field' => 'title'
    ])->call('updateTranslation', 'es', 'Título de Prueba')
      ->assertDispatched('translation-updated');
});
