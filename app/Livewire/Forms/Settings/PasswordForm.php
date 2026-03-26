<?php

namespace App\Livewire\Forms\Settings;

use App\Livewire\Actions\Settings\UpdatePassword;
use App\Models\User;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PasswordForm extends Form
{
    #[Validate('required|string|current_password')]
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function rules(): array
    {
        return [
            'password' => ['required', 'string', PasswordRule::defaults(), 'confirmed'],
        ];
    }

    public function update(User $user): void
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        app(UpdatePassword::class)->update($user, $this->password);

        $this->reset('current_password', 'password', 'password_confirmation');
    }
}
