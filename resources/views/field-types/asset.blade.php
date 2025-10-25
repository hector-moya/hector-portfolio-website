@php
    $value = $form->fieldValues[$element->handle] ?? null;
    $asset = $value ? \App\Models\Asset::find($value) : null;
@endphp

<div class="space-y-2" x-data="{ open: false }">
    {{-- Label & Instructions --}}
    <div>
        <flux:heading size="sm">{{ $element->label }}</flux:heading>
        @if($element->instructions)
            <flux:description>{{ $element->instructions }}</flux:description>
        @endif
    </div>

    {{-- Selected Asset Preview --}}
    @if($asset)
        <div class="relative w-24 h-24 rounded-lg overflow-hidden border-2 border-gray-200">
            @if($asset->is_image)
                <img src="{{ $asset->url }}" alt="{{ $asset->alt_text }}" class="w-full h-full object-cover">
            @else
                <div class="flex items-center justify-center w-full h-full bg-gray-100">
                    <flux:icon name="document" class="w-8 h-8 text-gray-400" />
                </div>
            @endif
            <button
                type="button"
                wire:click="form.fieldValues.{{ $element->handle }} = null"
                class="absolute top-1 right-1 p-1 rounded-full bg-red-500 text-white hover:bg-red-600"
            >
                <flux:icon name="x-mark" class="w-4 h-4" />
            </button>
        </div>
    @endif

    {{-- Asset Selector Button --}}
    <div>
        <flux:button
            type="button"
            variant="white"
            wire:click="$dispatch('open-asset-browser')"
        >
            <flux:icon name="document-image" class="w-4 h-4 mr-2" />
            {{ $asset ? 'Change Asset' : 'Select Asset' }}
        </flux:button>
    </div>

    {{-- Error State --}}
    <flux:error name="form.fieldValues.{{ $element->handle }}" />

    {{-- Asset Browser (Shared Component) --}}
    <livewire:assets.browser wire:key="asset-browser-{{ $element->handle }}" />
</div>
