<div>
    <div class="space-y-2">
        @if($value)
            <div class="relative group aspect-video w-48 rounded-lg border-2 border-gray-200 overflow-hidden">
                <img
                    src="{{ $value->url }}"
                    alt="{{ $value->alt_text }}"
                    class="w-full h-full object-cover"
                />
                <button
                    type="button"
                    wire:click="$set('{{ $handle }}', null)"
                    class="absolute top-2 right-2 p-1 bg-white rounded-full shadow opacity-0 group-hover:opacity-100 transition-opacity"
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
            {{ $value ? 'Change Image' : 'Select Image' }}
        </flux:button>

        @error($handle)
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <livewire:assets.upload-modal :key="'upload-modal-' . $handle" />
</div>
