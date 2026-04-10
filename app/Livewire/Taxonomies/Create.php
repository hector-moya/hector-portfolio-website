<?php

declare(strict_types=1);

namespace App\Livewire\Taxonomies;

use App\Livewire\Forms\TaxonomyForm;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Create extends Component
{
    public TaxonomyForm $form;

    public int $nextTempId = -1;

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
        $this->form->create();

        $this->redirect(route('taxonomies.index'), navigate: true);
    }

    public function render(): View|Factory
    {
        return view('livewire.taxonomies.create');
    }
}
