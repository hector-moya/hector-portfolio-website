<?php

namespace App\Livewire\Settings;

use App\Livewire\Forms\Settings\DeleteAccountForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteUserForm extends Component
{
    public DeleteAccountForm $form;

    public function deleteUser(): void
    {
        $this->form->destroy(Auth::user());

        $this->redirect('/', navigate: true);
    }
}
