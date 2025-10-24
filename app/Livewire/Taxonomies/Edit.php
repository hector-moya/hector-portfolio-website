<?php

namespace App\Livewire\Taxonomies;

use App\Livewire\Forms\TaxonomyForm;
use App\Models\Taxonomy;
use Livewire\Component;

class Edit extends Component
{
    public TaxonomyForm $form;

    public function mount(Taxonomy $taxonomy): void
    {
        $this->form->setTaxonomy($taxonomy);
    }

    public function save(): void
    {
        $this->form->update($this->form->taxonomy_id);
    }

    public function delete(): void
    {
        $this->form->destroy($this->form->taxonomy_id);

        $this->redirect(route('taxonomies.index'), navigate: true);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.taxonomies.edit');
    }
}
