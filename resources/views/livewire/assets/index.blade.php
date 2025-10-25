<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Assets</h1>

            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <flux:button wire:click="$dispatch('openModal', { component: 'assets.upload-modal' })" variant="primary">
                    Upload Files
                </flux:button>
            </div>
        </div>

        <div class="mt-8 bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
            <!-- Search and Filters -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 sm:flex sm:items-center sm:justify-between">
                <div class="sm:flex-1">
                    <flux:input
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search assets..."
                        type="search"
                    />
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-4 sm:flex-none">
                    <flux:select wire:model.live="filter">
                        <flux:select.option value="">All Files</flux:select.option>
                        <flux:select.option value="images">Images</flux:select.option>
                        <flux:select.option value="documents">Documents</flux:select.option>
                    </flux:select>
                </div>
            </div>

            <!-- Assets Grid -->
            <div class="p-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @forelse ($assets as $asset)
                    <div wire:key="asset-{{ $asset->id }}" class="relative group">
                        <div class="aspect-w-10 aspect-h-7 block w-full overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                            @if (str_starts_with($asset->mime_type, 'image/'))
                                <img src="{{ $asset->url }}" alt="{{ $asset->filename }}" class="object-cover">
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <flux:icon name="document" class="h-8 w-8 text-gray-400" />
                                </div>
                            @endif
                            <div class="absolute inset-0 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                <div class="absolute inset-x-0 bottom-0 bg-black bg-opacity-50 p-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <p class="text-sm text-white truncate">{{ $asset->filename }}</p>
                                </div>
                            </div>
                        </div>
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <flux:dropdown>
                                <flux:button size="xs">
                                    <flux:icon name="ellipsis-vertical" class="h-4 w-4" />
                                </flux:button>
                                <flux:menu>
                                    <flux:menu.item wire:click="download({{ $asset->id }})" icon="arrow-down-tray">
                                        Download
                                    </flux:menu.item>

                                    <flux:menu.item wire:click="move({{ $asset->id }})" icon="folder">
                                        Move
                                    </flux:menu.item>

                                    <flux:menu.item wire:click="delete({{ $asset->id }})" icon="trash" variant="danger">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="text-center py-12">
                            <flux:icon name="photo" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">No assets</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by uploading your first asset.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($assets->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $assets->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Move Asset Modal -->
    <flux:modal title="Move Asset" wire:model="showMoveModal">
        <div class="space-y-4">
            <flux:input
                wire:model="targetFolder"
                label="Target Folder"
                placeholder="Enter destination folder path"
            />
            <p class="text-sm text-gray-500">
                Use forward slashes (/) to specify nested folders. Example: /images/2025/october
            </p>
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showMoveModal', false)">
                    Cancel
                </flux:button>
                <flux:button wire:click="move">
                    Move Asset
                </flux:button>
            </div>
        </x-slot:footer>
    </flux:modal>

    <!-- Delete Asset Modal -->
    <flux:modal title="Delete Asset" wire:model="showDeleteModal">
        <div class="space-y-4">
            <p class="text-sm text-gray-500">
                Are you sure you want to delete this asset? This action cannot be undone.
            </p>
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showDeleteModal', false)">
                    Cancel
                </flux:button>
                <flux:button wire:click="delete">
                    Delete Asset
                </flux:button>
            </div>
        </x-slot:footer>
    </flux:modal>
</div>
