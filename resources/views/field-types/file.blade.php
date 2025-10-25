<div>
    <div class="space-y-2">
        @if($value)
            <div class="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-800 rounded-lg w-fit">
                <flux:icon.document class="w-5 h-5 text-gray-400" />
                <span class="text-sm text-gray-600 dark:text-gray-300">{{ $value->original_filename }}</span>
                <button
                    type="button"
                    wire:click="$set('{{ $handle }}', null)"
                    class="p-1 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-full"
                >
                    <flux:icon.x-mark class="w-4 h-4 text-gray-500" />
                </button>
            </div>
        @endif

        <flux:button
            type="button"

            size="sm"
            wire:click="$dispatch('open-asset-browser')"
        >
            {{ $value ? 'Change File' : 'Select File' }}
        </flux:button>

        @error($handle)
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <livewire:assets.upload-modal :key="'upload-modal-' . $handle" />
</div>
