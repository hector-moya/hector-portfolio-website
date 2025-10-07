<?php

namespace App\Livewire\Users;

use App\Livewire\Actions\Users\DeleteUser;
use App\Models\User;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $roleFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        $user = \App\Models\User::query()->findOrFail($id);

        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            $this->dispatch('error', message: 'You cannot delete yourself.');

            return;
        }

        (new DeleteUser)->execute($user);

        $this->dispatch('user-deleted');
    }

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    #[Title('Users')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        $users = User::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->roleFilter, fn ($query) => $query->where('role', $this->roleFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.users.index', [
            'users' => $users,
        ]);
    }
}
