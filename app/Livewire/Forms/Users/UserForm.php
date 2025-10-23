<?php

namespace App\Livewire\Forms\Users;

use App\Models\User;
use Illuminate\Validation\Rule;
use App\Livewire\Actions\Users\CreateUser;
use App\Livewire\Actions\Users\UpdateUser;
use App\Livewire\Actions\Users\DeleteUser;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password_confirmation = '';

    #[Validate('required|in:admin,editor,viewer')]
    public string $role = 'viewer';


    public function rules(): array
    {
        $rules['email'][] = Rule::unique(User::class, 'email')
            ->ignoreModel($this->user)
            ->whereNull('deleted_at');

        if ($this->user?->exists) {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function create(): User
    {
        $this->validate();

        $user = app(CreateUser::class)->create(
            userData: [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'role' => $this->role,
            ]);

            Flux::toast(
                heading: 'User Created',
                text: 'The user has been successfully created.',
                variant: 'success',
            );

        return $user;
    }

    public function update(int $userId): User
    {
        $this->validate();

        $user = app(UpdateUser::class)->update(
            userData: [
                'id' => $userId,
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'role' => $this->role,
            ]);

            Flux::toast(
                heading: 'User Updated',
                text: 'The user has been successfully updated.',
                variant: 'success',
            );

        return $user;
    }

    public function destroy(int $userId): void
    {
        if ($userId === auth()->id()) {
            Flux::toast(
                heading: 'Error',
                text: 'You cannot delete yourself.',
                variant: 'error',
            );

            return;
        }
        
        app(DeleteUser::class)->delete($userId);

        Flux::toast(
            heading: 'User Deleted',
            text: 'The user has been successfully deleted.',
            variant: 'success',
        );
    }
}
