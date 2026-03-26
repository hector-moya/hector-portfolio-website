<?php

namespace App\Livewire\Forms\Settings;

use App\Livewire\Actions\Settings\UpdateProfile;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProfileForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    public string $email = '';

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore(auth()->id()),
            ],
        ];
    }

    public function setUser(User $user): void
    {
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function update(User $user): User
    {
        $this->validate();

        return app(UpdateProfile::class)->update($user, [
            'name' => $this->name,
            'email' => $this->email,
        ]);
    }
}
