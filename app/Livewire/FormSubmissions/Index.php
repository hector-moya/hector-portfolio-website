<?php

namespace App\Livewire\FormSubmissions;

use App\Models\FormSubmission;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'read')]
    public string $readFilter = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function markAsRead(int $id): void
    {
        FormSubmission::query()->findOrFail($id)->markAsRead();
    }

    public function delete(int $id): void
    {
        FormSubmission::query()->findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Submission deleted.');
    }

    #[Computed]
    public function submissions(): LengthAwarePaginator
    {
        return FormSubmission::query()
            ->when($this->search, function ($query): void {
                $query->where(function ($q): void {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('message', 'like', "%{$this->search}%");
                });
            })
            ->when($this->readFilter === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->when($this->readFilter === 'read', fn ($q) => $q->whereNotNull('read_at'))
            ->latest()
            ->paginate(20);
    }

    #[Computed]
    public function unreadCount(): int
    {
        return FormSubmission::query()->whereNull('read_at')->count();
    }

    #[Title('Form Submissions')]
    public function render(): View|Factory
    {
        return view('livewire.form-submissions.index');
    }
}
