<div class="p-4">
    <div class="mb-4">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="Search images..."
            type="search"
        >
            <x-slot:prefix>
                <flux:icon name="magnifying-glass" class="h-4 w-4 text-gray-400" />
            </x-slot:prefix>
        </flux:input>
    </div>

    {{-- Image Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 max-h-[60vh] overflow-y-auto p-4">
        @forelse($assets as $asset)
            <button
                type="button"
                wire:click="selectAsset({{ $asset->id }})"
                class="aspect-square rounded-lg border-2 border-gray-200 overflow-hidden hover:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
            >
                <img
                    src="{{ $asset->url }}"
                    alt="{{ $asset->original_filename }}"
                    class="w-full h-full object-cover"
                />
            </button>
        @empty
            <div class="col-span-full text-center py-8 text-gray-500">
                No images found.
            </div>
        @endforelse
    </div>

    {{-- Upload Section --}}
    <div class="mt-4 border-t pt-4">
        <form wire:submit="uploadAssets">
            <flux:file-upload
                wire:model="uploads"
                multiple
                accept="image/*"
            >
                <flux:file-upload.dropzone
                    heading="Drop images here or click to upload"
                    text="JPG, PNG, GIF up to 10MB"
                />
            </flux:file-upload>
        </form>
    </div>

    {{-- Pagination --}}
    @if($assets->hasPages())
        <div class="mt-4">
            {{ $assets->links() }}
        </div>
    @endif
</div>
