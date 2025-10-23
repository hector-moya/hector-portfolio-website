<?php

namespace App\Livewire\Users;

use App\Livewire\Forms\Users\UserForm;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public UserForm $form;

    public function mount(): void
    {
        $this->authorize('create', User::class);
    }

    public function save(): void
    {
        $this->authorize('create', User::class);

        $this->form->create();

        $this->redirect(route('users.index'), navigate: true);
    }

    #[Title('Create User')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.users.create');
    }
}
