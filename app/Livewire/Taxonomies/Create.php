<?php

namespace App\Livewire\Taxonomies;

use App\Livewire\Forms\TaxonomyForm;
use Livewire\Component;

class Create extends Component
{
    public TaxonomyForm $form;

    public function save(): void
    {
        $taxonomy = $this->form->create();

        $this->redirect(route('taxonomies.edit', $taxonomy), navigate: true);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.taxonomies.create');
    }
}
