<?php

namespace App\Livewire\Fields;

use App\Models\Asset;
use App\Models\Field;
use Livewire\Attributes\Modelable;
use Livewire\Component;

/** @property array $fieldValues */
class ImageField extends Component
{
    public Field $element;

    #[Modelable]
    public array $fieldValues = [];

    public ?Asset $asset = null;

    public function mount(Field $element, array $fieldValues = []): void
    {
        $this->element = $element;
        $this->fieldValues = $fieldValues;

        // Load the asset if we have one
        $value = $fieldValues[$element->handle] ?? null;
        if ($value) {
            $this->asset = Asset::query()->find($value);
        }
    }

    public function openAssetBrowser(): void
    {
        // Your existing asset browser logic
    }

    public function removeImage(): void
    {
        $this->fieldValues[$this->element->handle] = null;
        $this->asset = null;
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.fields.image-field');
    }
}
