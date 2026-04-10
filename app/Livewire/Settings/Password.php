<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Livewire\Forms\Settings\PasswordForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class Password extends Component
{
    public PasswordForm $form;

    public function updatePassword(): void
    {
        $this->form->update(Auth::user());

        $this->dispatch('password-updated');
    }
}
