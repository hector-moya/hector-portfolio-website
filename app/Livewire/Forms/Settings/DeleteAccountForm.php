<?php

namespace App\Livewire\Forms\Settings;

use App\Livewire\Actions\Settings\DeleteAccount;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DeleteAccountForm extends Form
{
    #[Validate('required|string|current_password')]
    public string $password = '';

    public function destroy(User $user): void
    {
        $this->validate();

        app(DeleteAccount::class)->delete($user);
    }
}
