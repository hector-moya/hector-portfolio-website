<?php

namespace App\Livewire\Users;

use App\Livewire\Forms\Users\UserForm;
use App\Mail\UserApproved;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public UserForm $form;

    public string $search = '';

    public string $roleFilter = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return User::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->roleFilter, fn ($query) => $query->where('role', $this->roleFilter))
            ->tap(fn ($query) => $this->sortBy !== '' && $this->sortBy !== '0' ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->latest()
            ->paginate(10);
    }

    public function delete(int $userId): void
    {
        $this->form->destroy($userId);

        $this->dispatch('user-deleted');
    }

    public function approve(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        $this->authorize('approve', $user);

        $user->update(['status' => 'active']);

        Mail::to($user)->queue(new UserApproved($user));

        Flux::toast(
            heading: 'User Approved',
            text: "{$user->name} has been approved and notified.",
            variant: 'success',
        );
    }

    #[Title('Users')]
    public function render(): View|Factory
    {
        return view('livewire.users.index');
    }
}
