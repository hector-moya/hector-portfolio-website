<?php

declare(strict_types=1);

namespace App\Livewire\Navigation;

use App\Livewire\Forms\NavigationForm;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Create extends Component
{
    public NavigationForm $form;

    public function save(): void
    {
        $navigation = $this->form->create();

        $this->redirect(route('navigation.edit', $navigation), navigate: true);
    }

    public function render(): View|Factory
    {
        return view('livewire.navigation.create');
    }
}
