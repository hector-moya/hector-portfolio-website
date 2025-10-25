<div>
    <div class="mx-auto flex h-full w-full max-w-4xl flex-1 flex-col gap-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Assets') }}</flux:heading>
                <flux:text>{{ __('Manage your uploaded files and images') }}</flux:text>
            </div>

                <flux:modal.trigger name="upload-files">
                    <flux:button wire:click="$dispatch('openModal', { component: 'assets.upload-modal' })" variant="primary">
                        {{ __('Upload Files') }}
                    </flux:button>
                </flux:modal.trigger>
        </div>

        <div class="mt-8 overflow-hidden bg-white shadow sm:rounded-lg dark:bg-gray-800">
            <!-- Search and Filters -->
            <div class="border-b border-gray-200 p-4 sm:flex sm:items-center sm:justify-between dark:border-gray-700">
                <div class="sm:flex-1">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Search assets..." type="search" />
                </div>
                <div class="mt-4 sm:ml-4 sm:mt-0 sm:flex-none">
                    <flux:select wire:model.live="filter">
                        <flux:select.option value="">{{ __('All Files') }}</flux:select.option>
                        <flux:select.option value="images">{{ __('Images') }}</flux:select.option>
                        <flux:select.option value="documents">{{ __('Documents') }}</flux:select.option>
                    </flux:select>
                </div>
            </div>

            <!-- Assets Grid -->
            <div class="grid grid-cols-2 gap-4 p-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @forelse ($this->assets as $asset)
                    <div wire:key="asset-{{ $asset->id }}" class="group relative">
                        <div class="aspect-w-10 aspect-h-7 block w-full overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                            @if (str_starts_with($asset->mime_type, 'image/'))
                                <img src="{{ $asset->url }}" alt="{{ $asset->filename }}" class="object-cover">
                            @else
                                <div class="flex h-full items-center justify-center">
                                    <flux:icon name="document" class="h-8 w-8 text-gray-400" />
                                </div>
                            @endif
                            <div class="focus-within:ring-primary-500 absolute inset-0 focus-within:ring-2 focus-within:ring-offset-2">
                                <div class="absolute inset-x-0 bottom-0 bg-black bg-opacity-50 p-2 opacity-0 transition-opacity group-hover:opacity-100">
                                    <p class="truncate text-sm text-white">{{ $asset->filename }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute right-2 top-2 opacity-0 transition-opacity group-hover:opacity-100">
                            <flux:dropdown>
                                <flux:button size="xs">
                                    <flux:icon name="ellipsis-vertical" class="h-4 w-4" />
                                </flux:button>
                                <flux:menu>
                                    <flux:menu.item wire:click="download({{ $asset->id }})" icon="arrow-down-tray">
                                        {{ __('Download') }}
                                    </flux:menu.item>

                                    <flux:menu.item wire:click="move({{ $asset->id }})" icon="folder">
                                        {{ __('Move') }}
                                    </flux:menu.item>

                                    <flux:menu.item wire:click="delete({{ $asset->id }})" icon="trash" variant="danger">
                                        {{ __('Delete') }}
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="py-12 text-center">
                            <flux:icon name="photo" class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ __('No assets') }}</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Get started by uploading your first asset.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Move Asset Modal -->
    <flux:modal title="Move Asset" name="upload-files">
        <div class="space-y-4">
            <flux:input wire:model="targetFolder" label="Target Folder" placeholder="Enter destination folder path" />
            <p class="text-sm text-gray-500">
                {{ __('Use forward slashes (/) to specify nested folders. Example: /images/2025/october') }}
            </p>
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showMoveModal', false)">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button wire:click="move">
                    {{ __('Move Asset') }}
                </flux:button>
            </div>
        </x-slot:footer>
    </flux:modal>

    <!-- Delete Asset Modal -->
    <flux:modal title="Delete Asset" wire:model="showDeleteModal">
        <div class="space-y-4">
            <p class="text-sm text-gray-500">
                {{ __('Are you sure you want to delete this asset? This action cannot be undone.') }}
            </p>
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <flux:button wire:click="$set('showDeleteModal', false)">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button wire:click="delete">
                    {{ __('Delete Asset') }}
                </flux:button>
            </div>
        </x-slot:footer>
    </flux:modal>
</div>
