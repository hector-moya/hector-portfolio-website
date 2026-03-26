<?php

namespace App\Livewire\Settings;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

// TODO: Extract password field and validation into a DeleteAccountForm (app/Livewire/Forms/Settings/DeleteAccountForm.php,
//       extends Livewire\Form) with a #[Validate] attribute for current_password.
//       Add a destroy() method that calls a DeleteAccount action (app/Livewire/Actions/Settings/DeleteAccount.php).
//       The action should handle logging the user out (reusing the Logout action) and then deleting the model.
//       Note: this is distinct from app/Livewire/Actions/Users/DeleteUser.php which is for admins deleting
//       other users — this action is scoped to self-deletion by the authenticated user.
class DeleteUserForm extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}
