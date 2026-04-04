<?php

namespace App\Livewire\Settings;

use App\Models\SiteSetting;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

class Security extends Component
{
    public string $registrationMode = 'closed';

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);

        $this->registrationMode = SiteSetting::get('registration_mode', 'closed');
    }

    public function saveMode(): void
    {
        $this->authorize('viewAny', User::class);

        $this->validate([
            'registrationMode' => ['required', 'in:closed,invitation,approval,open'],
        ]);

        SiteSetting::set('registration_mode', $this->registrationMode);

        Flux::toast(
            heading: 'Settings Saved',
            text: 'Registration mode has been updated.',
            variant: 'success',
        );
    }

    #[Title('Security')]
    public function render(): View
    {
        return view('livewire.settings.security');
    }
}
