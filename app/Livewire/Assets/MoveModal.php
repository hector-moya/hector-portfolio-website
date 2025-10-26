<?php

namespace App\Livewire\Assets;

use Livewire\Component;
use App\Livewire\Forms\AssetForm;

class MoveModal extends Component
{
    public AssetForm $form;
    public ?string $targetFolder = null;
    public array $currentFolderPath = [];
    public ?int $assetId = null;

    public function mount(): void
    {
        $this->form->setAsset($this->assetId);
        $this->currentFolderPath = $this->buildCurrentPath();
    }

    public function buildCurrentPath(): array
    {
        return explode('/', trim($this->form->folder ?? '', '/'));
    }

    public function move(): void
    {
        $this->form->move($this->form->assetId, $this->targetFolder);

        $this->dispatch('asset-moved');
    }
    public function render()
    {
        return view('livewire.assets.move-modal');
    }
}
