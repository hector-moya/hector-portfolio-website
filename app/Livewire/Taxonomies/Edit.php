<?php

namespace App\Livewire\Taxonomies;

use App\Livewire\Forms\TaxonomyForm;
use App\Models\Taxonomy;
use Livewire\Component;

class Edit extends Component
{
    public TaxonomyForm $form;

    public int $nextTempId = -1;

    public function mount(Taxonomy $taxonomy): void
    {
        $this->form->setTaxonomy($taxonomy);
    }

    public function addTerm(): void
    {
        $this->form->terms[] = [
            'id' => $this->nextTempId--,
            'slug' => '',
            'name' => '',
            'parent_id' => null,
        ];
    }

    public function deleteTerm(int $termId): void
    {
        $this->form->terms = array_filter($this->form->terms, fn (array $term): bool => $term['id'] !== $termId);
    }

    public function save(): void
    {
        $this->form->update($this->form->taxonomy_id);
        $this->redirect(route('taxonomies.index'), navigate: true);
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
