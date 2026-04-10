<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Livewire\Forms\Settings\ProfileForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

final class Profile extends Component
{
    public ProfileForm $form;

    public function mount(): void
    {
        $this->form->setUser(Auth::user());
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $this->form->update($user);

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}
