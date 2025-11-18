@props([
    'index' => null,
    'parentHandle' => null,
    'field' => null,
])

@php
    $wirePath = $parentHandle !== null
        ? "form.fieldValues.{$parentHandle}.items.{$index}.{$field->handle}"
        : "form.fieldValues.{$field->handle}";

    $value = $parentHandle !== null
        ? ($form->fieldValues[$parentHandle]['items'][$index][$field->handle] ?? null)
        : ($form->fieldValues[$field->handle] ?? null);
    $asset = $value ? \App\Models\Asset::find($value) : null;
    $maxSize = $field->config['max_size_mb'] ?? 5;
    $mimes = $field->config['mimes'] ?? ['jpg', 'jpeg', 'png', 'webp'];
@endphp

<div class="space-y-2">
    {{-- Label & Instructions --}}
    <div>
        <flux:heading size="sm">{{ $field->label }}</flux:heading>
        @if ($field->instructions)
            <flux:description>{{ $field->instructions }}</flux:description>
        @endif
    </div>

    @if ($asset)
        <div class="group relative aspect-video w-48 overflow-hidden rounded-lg border-2 border-gray-200 dark:border-gray-700">
            <img src="{{ $asset->url }}" alt="{{ $asset->alt_text }}" class="h-full w-full object-cover" />
            <button type="button" wire:click="{{ $wirePath }} = null" class="absolute right-2 top-2 rounded-full bg-white p-1 opacity-0 shadow transition-opacity group-hover:opacity-100 dark:bg-gray-800">
                <flux:icon.x-mark class="h-4 w-4 text-gray-500" />
            </button>
        </div>
    @endif

    {{-- Asset Selector Button --}}
    <div>
        <flux:modal.trigger name="asset-browser-{{ $field->handle . $index }}">
            <flux:button type="button" variant="primary" icon="photo">
                {{ $asset ? 'Change Image' : 'Select Image' }}
            </flux:button>
        </flux:modal.trigger>
        <flux:text size="xs" class="mt-1 opacity-70">
            {{ __('Max size: :size MB. Accepted formats: :formats', [
                'size' => $maxSize,
                'formats' => implode(', ', $mimes),
            ]) }}
        </flux:text>
    </div>

    {{-- Asset Browser Modal --}}
    <flux:modal name="asset-browser-{{ $field->handle . $index }}">
        <livewire:assets.upload-modal :key="'upload-modal-' . $field->handle . $index" :currentFolderId="null" />
    </flux:modal>
</div>
