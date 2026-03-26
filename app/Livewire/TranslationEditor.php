<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class TranslationEditor extends Component
{
    public Model $model;

    public string $field;

    public array $translations = [];

    public array $locales = [
        'en' => 'English',
        'es' => 'Español',
    ];

    public function mount(Model $model, string $field): void
    {
        $this->model = $model;
        $this->field = $field;
        $this->loadTranslations();
    }

    public function loadTranslations(): void
    {
        if (! method_exists($this->model, 'getTranslations')) {
            $this->translations = [
                App::getLocale() => $this->model->{$this->field},
            ];

            return;
        }

        $this->translations = $this->model->getTranslations($this->field);
    }

    public function updateTranslation(string $locale, string $value): void
    {
        // TODO: Three issues here:
        //   1. No authorization check — any authenticated user can update any model's field via this component.
        //      Add a Gate check (e.g. Gate::authorize('update', $this->model)) before persisting.
        //   2. Direct model manipulation (setTranslation + save) should be extracted into an UpdateTranslation
        //      action (app/Actions/Translations/UpdateTranslation.php) so the persistence logic is not
        //      scattered inline across components.
        //   3. The locales array is hardcoded ('en', 'es') — this should come from a config value
        //      (e.g. config('app.supported_locales')) so adding a new locale doesn't require a code change.
        if (method_exists($this->model, 'setTranslation')) {
            $this->model->setTranslation($this->field, $locale, $value);
        } else {
            $this->model->{$this->field} = $value;
            $this->model->save();
        }

        $this->loadTranslations();
        $this->dispatch('translation-updated');
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.translation-editor');
    }
}
