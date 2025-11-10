@php
    $value = $form->fieldValues[$field->handle] ?? null;
    $asset = $value ? \App\Models\Asset::find($value) : null;
@endphp

<div>
    <div class="space-y-2">
        {{-- Label & Instructions --}}
        <div>
            <flux:heading size="sm">{{ $field->label }}</flux:heading>
            @if($field->instructions)
                <flux:description>{{ $field->instructions }}</flux:description>
            @endif
        </div>

        @if($asset)
            <div class="relative group aspect-video w-48 rounded-lg border-2 border-gray-200 overflow-hidden">
                <img
                    src="{{ $asset->url }}"
                    alt="{{ $asset->alt_text }}"
                    class="w-full h-full object-cover"
                />
                <button
                    type="button"
                    wire:click="form.fieldValues.{{ $field->handle }} = null"
                    class="absolute top-2 right-2 p-1 bg-white rounded-full shadow opacity-0 group-hover:opacity-100 transition-opacity"
                >
                    <flux:icon.x-mark class="w-4 h-4 text-gray-500" />
                </button>
            </div>
        @endif

        {{-- Asset Selector Button --}}
        <div>
            <flux:button
                type="button"
                variant="ghost"
                wire:click="openAssetBrowser('{{ $field->handle }}')"
            >
                <flux:icon name="photo" class="w-4 h-4 mr-2" />
                {{ $asset ? 'Change Image' : 'Select Image' }}
            </flux:button>
        </div>

        {{-- Error State --}}
        <flux:error name="form.fieldValues.{{ $field->handle }}" />

        {{-- Asset Browser Modal --}}
        <flux:modal name="asset-browser-{{ $field->handle }}">
            <livewire:assets.browser :key="'asset-browser-' . $field->handle" :field-handle="$field->handle" />
        </flux:modal>
    </div>
</div>
