<?php

namespace App\Livewire\Collections;

use App\Livewire\Actions\Collections\CreateCollection;
use App\Livewire\Forms\CollectionForm;
use App\Models\Blueprint;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public CollectionForm $form;

    public function save(): void
    {
        $this->form->validate();

        (new CreateCollection)->execute($this->form->all());

        session()->flash('message', 'Collection created successfully.');

        $this->redirect(route('collections.index'), navigate: true);
    }

    #[Title('Create Collection')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.collections.create', [
            'blueprints' => \App\Models\Blueprint::query()->where('is_active', true)->get(),
        ]);
    }
}
