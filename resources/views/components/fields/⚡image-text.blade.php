<?php

use App\Models\Asset;
use App\Models\Field;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public Field $field;

    #[Modelable]
    public array $data = [];

    public function mount(Field $field): void
    {
        $this->field = $field;
    }

    public function updatedData(): void
    {
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }

    #[On('asset-selected')]
    public function onAssetSelected(string $handle, mixed $value): void
    {
        if ($handle === $this->field->handle . '_image') {
            $this->data['image'] = $value;
            $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
        }
    }

    public function removeImage(): void
    {
        $this->data['image'] = null;
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }
};
?>

@php $imgAsset = ($data['image'] ?? null) ? \App\Models\Asset::find($data['image']) : null; @endphp

<div>
<div class="space-y-4">
    <flux:input label="{{ __('Title') }}" wire:model.blur="data.title" />
    <flux:textarea label="{{ __('Content') }}" wire:model.blur="data.content" rows="4" />
    <flux:select label="{{ __('Image Position') }}" wire:model.live="data.image_position">
        <flux:select.option value="left">{{ __('Left') }}</flux:select.option>
        <flux:select.option value="right">{{ __('Right') }}</flux:select.option>
    </flux:select>

    {{-- Image --}}
    <div class="space-y-2">
        <flux:label>{{ __('Image') }}</flux:label>
        @if ($imgAsset)
            <div class="group relative w-48 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <img src="{{ $imgAsset->thumbnail_url ?? $imgAsset->url }}" alt="{{ $imgAsset->alt_text ?? '' }}" class="aspect-video w-full object-cover" />
                <button type="button" wire:click="removeImage" class="absolute right-2 top-2 rounded-full bg-white p-1 opacity-0 shadow transition-opacity group-hover:opacity-100 dark:bg-zinc-800">
                    <flux:icon.x-mark class="size-4 text-zinc-500" />
                </button>
            </div>
        @endif
        <flux:modal.trigger name="asset-browser-{{ $field->handle }}_image">
            <flux:button type="button" variant="ghost" size="sm" icon="photo">
                {{ $imgAsset ? __('Change Image') : __('Select Image') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <flux:input label="{{ __('CTA Text') }}" wire:model.blur="data.cta_text" />
        <flux:input label="{{ __('CTA URL') }}" wire:model.blur="data.cta_url" placeholder="/" />
    </div>
</div>

<flux:modal name="asset-browser-{{ $field->handle }}_image" max-width="3xl">
    <livewire:assets.browser :fieldHandle="$field->handle . '_image'" :wire:key="$field->handle . '_image_browser'" />
</flux:modal>
</div>
