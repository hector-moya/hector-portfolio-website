<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public string $currentLocale;

    public string $currentRoute;

    public array $locales = [
        'en' => 'English',
        'es' => 'Español',
    ];

    public function mount(): void
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLocale(string $locale): void
    {
        if (array_key_exists($locale, $this->locales)) {
            Session::put('locale', $locale);
            App::setLocale($locale);
            $this->currentLocale = $locale;
            $this->redirect(route($this->currentRoute));
            $this->dispatch('language-changed');
        }
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.language-switcher');
    }
}
