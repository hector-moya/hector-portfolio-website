<div>
    <div class="space-y-2">
        {{-- Label & Instructions --}}
        <div>
            <flux:heading size="sm">{{ $element->label }}</flux:heading>
            @if($element->instructions)
                <flux:description>{{ $element->instructions }}</flux:description>
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
                    wire:click="removeImage"
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
                wire:click="openAssetBrowser"
            >
                <flux:icon name="photo" class="w-4 h-4 mr-2" />
                {{ $asset ? 'Change Image' : 'Select Image' }}
            </flux:button>
        </div>

        {{-- Error State --}}
        <flux:error name="form.fieldValues.{{ $element->handle }}" />

        {{-- Asset Browser Modal --}}
        <flux:modal name="asset-browser-{{ $element->handle }}">
            <livewire:assets.browser :key="'asset-browser-' . $element->handle" :field-handle="$element->handle" />
        </flux:modal>
    </div>
</div>
