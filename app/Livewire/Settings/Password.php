<?php

namespace App\Livewire\Settings;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

// TODO: Extract password fields and validation into a PasswordForm (app/Livewire/Forms/Settings/PasswordForm.php,
//       extends Livewire\Form) with #[Validate] attributes for current_password, password, and password_confirmation.
//       Add an update() method that calls an UpdatePassword action (app/Livewire/Actions/Settings/UpdatePassword.php).
//       The action should handle Hash::make(), model update, and event dispatch.
//       The form's update() should re-throw the ValidationException after resetting fields so the component
//       stays thin and only handles flow control, not persistence — matching the pattern in UserForm.
class Password extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}
