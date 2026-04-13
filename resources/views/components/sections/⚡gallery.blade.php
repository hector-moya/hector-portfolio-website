<?php

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

    #[On('asset-selected')]
    public function onAssetSelected(string $handle, mixed $value): void
    {
        if ($handle === $this->field->handle . '_gallery') {
            $this->data['images'][] = $value;
        }
    }

    public function removeGalleryImage(int $index): void
    {
        array_splice($this->data['images'], $index, 1);
        $this->data['images'] = array_values($this->data['images']);
    }
};
?>

@php $images = $data['images'] ?? []; $gallerySlot = count($images); @endphp

<div>
<div class="space-y-4">
    <flux:input label="{{ __('Title') }}" wire:model.blur="data.title" />

    <div>
        <flux:label>{{ __('Images') }} <span class="text-zinc-400">({{ $gallerySlot }}/6)</span></flux:label>
        <div class="mt-2 grid grid-cols-3 gap-3 sm:grid-cols-6">
            @foreach ($images as $imgIdx => $assetId)
                @php $galleryAsset = \App\Models\Asset::find($assetId); @endphp
                <div wire:key="gallery-{{ $field->handle }}-{{ $imgIdx }}" class="group relative aspect-square overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    @if ($galleryAsset)
                        <img src="{{ $galleryAsset->thumbnail_url ?? $galleryAsset->url }}" alt="{{ $galleryAsset->alt_text ?? '' }}" class="h-full w-full object-cover" />
                    @endif
                    <button type="button" wire:click="removeGalleryImage({{ $imgIdx }})" class="absolute right-1 top-1 rounded-full bg-white p-0.5 opacity-0 shadow transition-opacity group-hover:opacity-100 dark:bg-zinc-800">
                        <flux:icon.x-mark class="size-3 text-zinc-500" />
                    </button>
                </div>
            @endforeach

            @if ($gallerySlot < 6)
                <flux:modal.trigger name="asset-browser-{{ $field->handle }}_gallery">
                    <button type="button" class="flex aspect-square items-center justify-center rounded-lg border-2 border-dashed border-zinc-300 text-zinc-400 transition-colors hover:border-teal-400 hover:text-teal-500 dark:border-zinc-600 dark:hover:border-teal-500">
                        <flux:icon.plus class="size-6" />
                    </button>
                </flux:modal.trigger>
            @endif
        </div>
    </div>
</div>

<flux:modal name="asset-browser-{{ $field->handle }}_gallery" max-width="3xl">
    <livewire:assets.browser :fieldHandle="$field->handle . '_gallery'" :wire:key="$field->handle . '_gallery_browser'" />
</flux:modal>
</div>
