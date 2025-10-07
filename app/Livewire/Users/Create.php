<?php

namespace App\Livewire\Users;

use App\Livewire\Actions\Users\CreateUser;
use App\Livewire\Forms\Users\UserForm;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public UserForm $form;

    public function mount(): void
    {
        $this->authorize('create', \App\Models\User::class);
    }

    public function save(): void
    {
        $this->authorize('create', \App\Models\User::class);

        $this->form->validate();

        (new CreateUser)->execute($this->form->all());

        session()->flash('message', 'User created successfully.');

        $this->redirect(route('users.index'), navigate: true);
    }

    #[Title('Create User')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.users.create');
    }
}
