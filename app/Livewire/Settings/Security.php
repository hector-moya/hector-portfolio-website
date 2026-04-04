<?php

namespace App\Livewire\Settings;

use App\Livewire\Actions\Users\InviteUser;
use App\Models\Invitation;
use App\Models\SiteSetting;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class Security extends Component
{
    public string $registrationMode = 'closed';

    public string $inviteEmail = '';

    public string $inviteRole = 'viewer';

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

    public function sendInvite(): void
    {
        $this->authorize('viewAny', User::class);

        $this->validate([
            'inviteEmail' => ['required', 'email', 'unique:invitations,email'],
            'inviteRole' => ['required', 'in:admin,editor,viewer'],
        ]);

        app(InviteUser::class)->invite(
            email: $this->inviteEmail,
            role: $this->inviteRole,
        );

        $this->reset('inviteEmail', 'inviteRole');
        $this->inviteRole = 'viewer';

        Flux::toast(
            heading: 'Invitation Sent',
            text: 'The invitation email has been sent.',
            variant: 'success',
        );
    }

    #[Computed]
    public function invitations(): Collection
    {
        return Invitation::with('invitedBy')
            ->orderByDesc('created_at')
            ->get();
    }

    #[Title('Security')]
    public function render(): View
    {
        return view('livewire.settings.security');
    }
}
