@php
    $value = $form->fieldValues[$field->handle] ?? null;
    $asset = $value ? \App\Models\Asset::find($value) : null;
    $maxSize = $field->config['max_size_mb'] ?? 5;
    $mimes = $field->config['mimes'] ?? ['jpg', 'jpeg', 'png', 'webp'];
@endphp

<div class="space-y-2">
    {{-- Label & Instructions --}}
    <div>
        <flux:heading size="sm">{{ $field->label }}</flux:heading>
        @if($field->instructions)
            <flux:description>{{ $field->instructions }}</flux:description>
        @endif
    </div>

    @if($asset)
        <div class="relative group aspect-video w-48 rounded-lg border-2 border-gray-200 dark:border-gray-700 overflow-hidden">
            <img
                src="{{ $asset->url }}"
                alt="{{ $asset->alt_text }}"
                class="w-full h-full object-cover"
            />
            <button
                type="button"
                wire:click="form.fieldValues.{{ $field->handle }} = null"
                class="absolute top-2 right-2 p-1 bg-white dark:bg-gray-800 rounded-full shadow opacity-0 group-hover:opacity-100 transition-opacity"
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
            <flux:icon.photo class="w-4 h-4 mr-2" />
            {{ $asset ? 'Change Image' : 'Select Image' }}
        </flux:button>
        <flux:text size="xs" class="mt-1 opacity-70">
            {{ __('Max size: :size MB. Accepted formats: :formats', [
                'size' => $maxSize,
                'formats' => implode(', ', $mimes)
            ]) }}
        </flux:text>
    </div>

    {{-- Error State --}}
    <flux:error name="form.fieldValues.{{ $field->handle }}" />

    {{-- Asset Browser Modal --}}
    <flux:modal name="asset-browser-{{ $field->handle }}">
        <livewire:assets.browser :key="'asset-browser-' . $field->handle" :field-handle="$field->handle" />
    </flux:modal>
</div>
