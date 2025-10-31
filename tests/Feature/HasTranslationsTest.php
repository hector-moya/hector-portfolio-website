<?php

use App\Models\Concerns\HasTranslations;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class TranslatableModel extends Model
{
    use HasTranslations;

    protected $fillable = ['title'];
    protected $table = 'entries';
}

test('models can get and set translations', function () {
    $model = new TranslatableModel(['title' => 'Test Title']);
    $model->save();

    $model->setTranslation('title', 'es', 'Título de Prueba');
    expect($model->translate('title', 'es'))->toBe('Título de Prueba');
});

test('models fallback to default locale when translation missing', function () {
    $model = new TranslatableModel(['title' => 'Test Title']);
    $model->save();

    expect($model->translate('title', 'es'))->toBe('Test Title');
});

test('models can get all translations', function () {
    $model = new TranslatableModel(['title' => 'Test Title']);
    $model->save();

    $model->setTranslation('title', 'es', 'Título de Prueba');
    $model->setTranslation('title', 'fr', 'Titre de Test');

    $translations = $model->getTranslations('title');

    expect($translations)->toBe([
        'en' => 'Test Title',
        'es' => 'Título de Prueba',
        'fr' => 'Titre de Test'
    ]);
});
