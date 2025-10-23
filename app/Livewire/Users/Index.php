<?php

namespace App\Livewire\Users;

use Livewire\Attributes\Computed;
use App\Models\User;
use Livewire\Attributes\Title;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use App\Livewire\Forms\Users\UserForm;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public UserForm $form;

    public string $search = '';

    public string $roleFilter = '';

    public string $sortBy = 'date';

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

    #[Title('Users')]
    public function render(): View|Factory
    {
        return view('livewire.users.index');
    }
}
