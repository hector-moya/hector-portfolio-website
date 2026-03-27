<div class="p-4 space-y-4">
    {{-- Search --}}
    <flux:input
        wire:model.live.debounce.300ms="search"
        placeholder="{{ $mode === 'image' ? __('Search images...') : __('Search files...') }}"
        type="search"
    >
        <x-slot:prefix>
            <flux:icon name="magnifying-glass" class="h-4 w-4 text-gray-400" />
        </x-slot:prefix>
    </flux:input>

    {{-- Breadcrumbs --}}
    <div class="flex items-center gap-1 text-sm flex-wrap">
        <button type="button" wire:click="openFolder(null)" class="text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200">
            {{ __('All Files') }}
        </button>
        @foreach ($this->breadcrumbs as $crumb)
            <flux:icon name="chevron-right" size="micro" class="text-zinc-400" />
            <button
                type="button"
                wire:click="openFolder({{ $crumb['id'] }})"
                class="text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200"
            >
                {{ $crumb['name'] }}
            </button>
        @endforeach
        @if ($currentFolderId)
            @php $currentFolder = \App\Models\Folder::find($currentFolderId) @endphp
            @if ($currentFolder)
                <flux:icon name="chevron-right" size="micro" class="text-zinc-400" />
                <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $currentFolder->name }}</span>
            @endif
        @endif
    </div>

    {{-- Folder list --}}
    @if ($this->folders->isNotEmpty())
        <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
            @foreach ($this->folders as $folder)
                <button
                    type="button"
                    wire:click="openFolder({{ $folder->id }})"
                    class="flex items-center gap-2 rounded-lg border px-3 py-2 text-left text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                >
                    <flux:icon name="folder" size="micro" class="shrink-0 text-zinc-400" />
                    <span class="truncate">{{ $folder->name }}</span>
                </button>
            @endforeach
        </div>
        <flux:separator />
    @endif

    {{-- Asset grid --}}
    @if ($mode === 'image')
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 max-h-[50vh] overflow-y-auto">
            @forelse($assets as $asset)
                <button
                    type="button"
                    wire:click="selectAsset({{ $asset->id }})"
                    class="group relative aspect-square rounded-lg border-2 border-zinc-200 dark:border-zinc-700 overflow-hidden hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                >
                    <img
                        src="{{ $asset->thumbnail_url ?? $asset->url }}"
                        alt="{{ $asset->alt_text ?? $asset->original_filename }}"
                        class="w-full h-full object-cover"
                    />
                    <div class="absolute inset-x-0 bottom-0 bg-black/50 px-1 py-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                        <p class="truncate text-xs text-white">{{ $asset->original_filename }}</p>
                    </div>
                </button>
            @empty
                <div class="col-span-full py-8 text-center text-sm text-zinc-500">
                    {{ __('No images found.') }}
                </div>
            @endforelse
        </div>
    @else
        <div class="max-h-[50vh] overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800">
            @forelse($assets as $asset)
                <button
                    type="button"
                    wire:click="selectAsset({{ $asset->id }})"
                    class="flex w-full items-center gap-3 px-2 py-2 text-left hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                >
                    <flux:icon name="document" size="micro" class="shrink-0 text-zinc-400" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm">{{ $asset->original_filename }}</p>
                        <p class="text-xs text-zinc-400">{{ $asset->size_for_humans }}</p>
                    </div>
                </button>
            @empty
                <div class="py-8 text-center text-sm text-zinc-500">
                    {{ __('No files found.') }}
                </div>
            @endforelse
        </div>
    @endif

    {{-- Pagination --}}
    @if ($assets->hasPages())
        <div class="mt-2">
            {{ $assets->links() }}
        </div>
    @endif
</div>
