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
