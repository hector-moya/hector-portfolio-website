<?php

namespace App\Livewire\Collections;

use App\Livewire\Actions\Collections\UpdateCollection;
use App\Livewire\Forms\CollectionForm;
use App\Models\Blueprint;
use App\Models\Collection;
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
        $this->form->validate();

        (new UpdateCollection)->execute($this->collection, $this->form->all());

        session()->flash('message', 'Collection updated successfully.');

        $this->redirect(route('collections.index'), navigate: true);
    }

    #[Title('Edit Collection')]
    public function render()
    {
        return view('livewire.collections.edit', [
            'blueprints' => Blueprint::where('is_active', true)->get(),
        ]);
    }
}
