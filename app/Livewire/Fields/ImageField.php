<?php

namespace App\Livewire\Fields;

use App\Models\Asset;
use App\Models\BlueprintElement;
use Livewire\Component;
use Livewire\Form;

class ImageField extends Component
{
    public BlueprintElement $element;
    public Form $form;
    public ?Asset $asset = null;

    public function mount(BlueprintElement $element, Form $form)
    {
        $this->element = $element;
        $this->form = $form;

        // Load the asset if we have one
        $value = $form->fieldValues[$element->handle] ?? null;
        if ($value) {
            $this->asset = Asset::find($value);
        }
    }

    public function openAssetBrowser(): void
    {
        // Your existing asset browser logic
    }

    public function removeImage(): void
    {
        $this->form->fieldValues[$this->element->handle] = null;
        $this->asset = null;
    }

    public function render()
    {
        return view('livewire.fields.image-field');
    }
}
