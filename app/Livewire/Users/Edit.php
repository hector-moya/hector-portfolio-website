<?php

namespace App\Livewire\Users;

use App\Livewire\Actions\Users\UpdateUser;
use App\Livewire\Forms\Users\UserForm;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component
{
    public UserForm $form;

    public User $user;

    public function mount(User $user): void
    {
        $this->authorize('update', $user);

        $this->user = $user;
        $this->form->setUser($user);
    }

    public function save(): void
    {
        $this->authorize('update', $this->user);

        $this->form->validate();

        (new UpdateUser)->execute($this->user, $this->form->all());

        session()->flash('message', 'User updated successfully.');

        $this->redirect(route('users.index'), navigate: true);
    }

    #[Title('Edit User')]
    public function render()
    {
        return view('livewire.users.edit');
    }
}
