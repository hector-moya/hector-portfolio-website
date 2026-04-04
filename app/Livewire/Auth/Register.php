<?php

namespace App\Livewire\Auth;

use App\Mail\AdminPendingUserNotification;
use App\Models\Invitation;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    #[Url]
    #[Locked]
    public string $token = '';

    public function mount(): void
    {
        if ($this->token) {
            $invitation = Invitation::where('token', $this->token)
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->first();

            if ($invitation) {
                $this->email = $invitation->email;
            }
        }
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $mode = SiteSetting::get('registration_mode', 'closed');

        if ($mode === 'invitation' && $this->token) {
            $invitation = Invitation::where('token', $this->token)
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->first();

            if (! $invitation || $invitation->email !== $validated['email']) {
                $this->addError('email', 'This invitation is not valid for the provided email address.');

                return;
            }
        }

        if ($mode === 'approval') {
            $validated['status'] = 'pending';
        }

        $user = User::query()->create($validated);

        event(new Registered($user));

        if ($mode === 'approval') {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Mail::to($admin)->queue(new AdminPendingUserNotification($user));
            }
        }

        if ($mode === 'invitation' && $this->token) {
            Invitation::where('token', $this->token)
                ->whereNull('accepted_at')
                ->update(['accepted_at' => now()]);
        }

        Auth::login($user);

        Session::regenerate();

        $this->redirect(route('verification.notice', absolute: false), navigate: true);
    }
}
