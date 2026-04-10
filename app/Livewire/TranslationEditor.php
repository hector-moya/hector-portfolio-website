<?php

namespace App\Livewire;

use App\Actions\Translations\UpdateTranslation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class TranslationEditor extends Component
{
    public Model $model;

    public string $field;

    public array $translations = [];

    public function mount(Model $model, string $field): void
    {
        $this->model = $model;
        $this->field = $field;
        $this->loadTranslations();
    }

    public function locales(): array
    {
        return config('app.supported_locales', ['en' => 'English']);
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
        resolve(UpdateTranslation::class)->handle($this->model, $this->field, $locale, $value);

        $this->loadTranslations();
        $this->dispatch('translation-updated');
    }

    public function render(): View|Factory
    {
        return view('livewire.translation-editor', [
            'locales' => $this->locales(),
        ]);
    }
}
