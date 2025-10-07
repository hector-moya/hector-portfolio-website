<?php

namespace App\Livewire\Entries;

use App\Models\Entry;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Preview extends Component
{
    public bool $show = false;

    public ?int $entryId = null;

    #[On('preview-entry')]
    public function preview(int $entryId): void
    {
        $this->entryId = $entryId;
        $this->show = true;
    }

    public function close(): void
    {
        $this->show = false;
        $this->entryId = null;
    }

    #[Computed]
    public function entry(): ?Entry
    {
        if ($this->entryId === null || $this->entryId === 0) {
            return null;
        }

        return Entry::with(['collection', 'blueprint', 'author', 'elements.blueprintElement'])
            ->find($this->entryId);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.entries.preview');
    }
}
