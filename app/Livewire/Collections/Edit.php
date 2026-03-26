<?php

namespace App\Livewire\Collections;

use App\Livewire\Forms\CollectionForm;
use App\Models\Blueprint;
use App\Models\Collection;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component
{
    public CollectionForm $form;

    public Collection $collection;

    public function mount(Collection $collection): void
    {
        $this->collection = $collection;
        $this->form->setCollection($collection);
    }

    public function save(): void
    {
        $this->form->update($this->collection);

        $this->redirect(route('collections.index'), navigate: true);
    }

    #[Title('Edit Collection')]
    public function render(): View|Factory
    {
        return view('livewire.collections.edit', [
            'blueprints' => Blueprint::query()->where('is_active', true)->get(),
        ]);
    }
}
