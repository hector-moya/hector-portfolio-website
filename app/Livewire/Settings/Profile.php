<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;

// TODO: Extract profile fields and validation into a ProfileForm (app/Livewire/Forms/Settings/ProfileForm.php,
//       extends Livewire\Form) with #[Validate] attributes and a rules() method for the unique-email-with-ignore rule.
//       Add setUser(User $user) to populate the form on mount.
//       Add an update() method that calls an UpdateProfile action (app/Livewire/Actions/Settings/UpdateProfile.php).
//       The action should handle the email-dirty check (nulling email_verified_at), model save, and event dispatch.
//       Move resendVerificationNotification() logic into a ResendVerificationNotification action for reusability.
//       This aligns with the pattern in Users/Edit.php and removes all controller-like persistence from the component.
class Profile extends Component
{
    public string $name = '';

    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
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
