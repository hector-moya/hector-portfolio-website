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
        if ($handle === $this->field->handle . '_bg_image') {
            $this->data['bg_image'] = $value;
            $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
        }
    }

    public function removeBgImage(): void
    {
        $this->data['bg_image'] = null;
        $this->dispatch('field-data-updated', handle: $this->field->handle, data: $this->data);
    }
};
?>

@php $bgAsset = ($data['bg_image'] ?? null) ? \App\Models\Asset::find($data['bg_image']) : null; @endphp

<div>
<div class="space-y-4">
    <flux:input label="{{ __('Title') }}" wire:model.blur="data.title" />
    <flux:input label="{{ __('Subtitle') }}" wire:model.blur="data.subtitle" />
    <flux:textarea label="{{ __('Content') }}" wire:model.blur="data.content" rows="3" />

    {{-- Background Image --}}
    <div class="space-y-2">
        <flux:label>{{ __('Background Image') }}</flux:label>
        @if ($bgAsset)
            <div class="group relative w-48 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                <img src="{{ $bgAsset->thumbnail_url ?? $bgAsset->url }}" alt="{{ $bgAsset->alt_text ?? '' }}" class="aspect-video w-full object-cover" />
                <button type="button" wire:click="removeBgImage" class="absolute right-2 top-2 rounded-full bg-white p-1 opacity-0 shadow transition-opacity group-hover:opacity-100 dark:bg-zinc-800">
                    <flux:icon.x-mark class="size-4 text-zinc-500" />
                </button>
            </div>
        @endif
        <flux:modal.trigger name="asset-browser-{{ $field->handle }}_bg_image">
            <flux:button type="button" variant="ghost" size="sm" icon="photo">
                {{ $bgAsset ? __('Change Image') : __('Select Image') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <flux:input label="{{ __('Primary CTA Text') }}" wire:model.blur="data.cta_text" />
        <flux:input label="{{ __('Primary CTA URL') }}" wire:model.blur="data.cta_url" placeholder="/" />
    </div>
    <div class="grid grid-cols-2 gap-4">
        <flux:input label="{{ __('Secondary CTA Text') }}" wire:model.blur="data.secondary_cta_text" />
        <flux:input label="{{ __('Secondary CTA URL') }}" wire:model.blur="data.secondary_cta_url" placeholder="/" />
    </div>
</div>

<flux:modal name="asset-browser-{{ $field->handle }}_bg_image" max-width="3xl">
    <livewire:assets.browser :fieldHandle="$field->handle . '_bg_image'" :wire:key="$field->handle . '_bg_image_browser'" />
</flux:modal>
</div>
