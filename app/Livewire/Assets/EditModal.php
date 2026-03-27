<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Services\AssetUsage;
use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class EditModal extends Component
{
    public ?int $assetId = null;

    public string $title = '';

    public string $alt_text = '';

    public string $caption = '';

    public string $description = '';

    public string $copyright = '';

    #[On('open-asset-edit')]
    public function open(int $assetId): void
    {
        $asset = Asset::query()->findOrFail($assetId);

        Gate::authorize('update', $asset);

        $this->assetId = $asset->id;
        $this->title = $asset->title ?? '';
        $this->alt_text = $asset->alt_text ?? '';
        $this->caption = $asset->caption ?? '';
        $this->description = $asset->description ?? '';
        $this->copyright = $asset->copyright ?? '';

        Flux::modal('edit-asset')->show();
    }

    #[Computed]
    public function asset(): ?Asset
    {
        if ($this->assetId === null) {
            return null;
        }

        return Asset::query()->find($this->assetId);
    }

    #[Computed]
    public function usageCount(): int
    {
        if ($this->assetId === null) {
            return 0;
        }

        return app(AssetUsage::class)->count($this->assetId);
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
            'description' => 'nullable|string',
            'copyright' => 'nullable|string|max:255',
        ]);

        /** @var Asset $asset */
        $asset = Asset::query()->findOrFail($this->assetId);

        Gate::authorize('update', $asset);

        $asset->update([
            'title' => $this->title ?: null,
            'alt_text' => $this->alt_text ?: null,
            'caption' => $this->caption ?: null,
            'description' => $this->description ?: null,
            'copyright' => $this->copyright ?: null,
            'updated_by' => auth()->id(),
        ]);

        Flux::toast(
            heading: 'Asset Updated',
            text: 'The asset metadata has been saved.',
            variant: 'success',
        );

        Flux::modal('edit-asset')->close();

        $this->dispatch('asset-updated');
    }

    public function render(): View|Factory
    {
        return view('livewire.assets.edit-modal');
    }
}
