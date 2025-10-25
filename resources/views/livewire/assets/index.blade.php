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

        <!-- Search and Filters -->
        <div class="py-4 sm:flex sm:items-center sm:justify-between">
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
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
            @forelse ($this->assets as $asset)
                <div wire:key="asset-{{ $asset->id }}" class="group relative">
                    <div class="aspect-w-10 aspect-h-7 block w-full overflow-hidden rounded-lg bg-gray-100 dark:bg-gray-700">
                        @if (str_starts_with($asset->mime_type, 'image/'))
                            <img src="{{ $asset->url }}" alt="{{ $asset->original_filename }}" class="object-cover">
                        @else
                            <div class="flex h-full items-center justify-center">
                                <flux:icon name="document" class="h-8 w-8 text-gray-400" />
                            </div>
                        @endif
                        <div class="focus-within:ring-primary-500 absolute inset-0 focus-within:ring-2 focus-within:ring-offset-2">
                            <div class="absolute inset-x-0 bottom-0 bg-black bg-opacity-50 p-2 opacity-0 transition-opacity group-hover:opacity-100">
                                <p class="truncate text-sm text-white">{{ $asset->original_filename }}</p>
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

                                <flux:menu.item wire:click="openMoveAssetModal({{ $asset->id }})" icon="folder">
                                    {{ __('Move') }}
                                </flux:menu.item>

                                <flux:menu.item wire:click="delete({{ $asset->id }})" icon="trash" variant="danger" wire:confirm="{{ __('Are you sure you want to delete asset ') . $asset->original_filename . '?' }}">
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
    <flux:modal name="upload-files">
        <livewire:assets.upload-modal :key="'upload-modal-' . $uploadModalKey" />
    </flux:modal>

    <!-- Move Asset Modal -->
    <flux:modal title="Move Asset" name="move-asset">
            <livewire:assets.move-modal/>
    </flux:modal>
</div>
