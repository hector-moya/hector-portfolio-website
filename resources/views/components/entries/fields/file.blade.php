@php
    $value = $form->fieldValues[$field->handle] ?? null;
    $asset = $value ? \App\Models\Asset::find($value) : null;
    $maxSize = $field->config['max_size_mb'] ?? 10;
    $mimes = $field->config['mimes'] ?? ['pdf', 'doc', 'docx', 'csv', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
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
        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex-shrink-0">
                <flux:icon.document class="w-8 h-8 text-gray-400" />
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                    {{ $asset->filename }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ number_format($asset->file_size / 1024 / 1024, 2) }} MB
                </p>
            </div>
            <button
                type="button"
                wire:click="form.fieldValues.{{ $field->handle }} = null"
                class="flex-shrink-0 p-1 text-gray-400 hover:text-red-500 transition-colors"
            >
                <flux:icon.x-mark class="w-5 h-5" />
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
            <flux:icon.document class="w-4 h-4 mr-2" />
            {{ $asset ? 'Change File' : 'Select File' }}
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
