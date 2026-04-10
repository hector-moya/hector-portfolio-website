<?php

declare(strict_types=1);

namespace App\Livewire\Entries;

use App\Actions\Entries\ExportEntries;
use App\Models\Collection;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class Export extends Component
{
    public ?int $collectionId = null;

    public bool $includeBlueprint = true;

    #[Computed]
    public function collections(): \Illuminate\Database\Eloquent\Collection
    {
        return Collection::query()->orderBy('name')->get();
    }

    public function export(): StreamedResponse
    {
        $payload = resolve(ExportEntries::class)->handle($this->collectionId, $this->includeBlueprint);

        $filename = 'entries-export-'.now()->format('Y-m-d').'.json';

        return response()->streamDownload(function () use ($payload): void {
            echo $payload;
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function render(): View|Factory
    {
        return view('livewire.entries.export');
    }
}
