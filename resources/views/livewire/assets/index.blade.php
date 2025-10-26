<div>
    <div class="mx-auto flex h-full w-full max-w-4xl flex-1 flex-col gap-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Assets') }}</flux:heading>
                <flux:text>{{ __('Manage your uploaded files and images') }}</flux:text>
            </div>

            <flux:modal.trigger name="upload-files">
                <flux:button variant="primary">
                    {{ __('Upload Files') }}
                </flux:button>
            </flux:modal.trigger>
        </div>

        <div class="mt-4 flex items-center justify-between">

            <div class="flex w-full items-center gap-2">
                <div class="flex-grow">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="Search assets..." type="search" />
                </div>
                <div class="flex-1">
                    <flux:select wire:model.live="filter">
                        <flux:select.option value="">{{ __('All Files') }}</flux:select.option>
                        <flux:select.option value="images">{{ __('Images') }}</flux:select.option>
                        <flux:select.option value="documents">{{ __('Documents') }}</flux:select.option>
                    </flux:select>
                </div>
                <flux:button :disabled="count($selected) === 0" wire:click="openMoveAssetModal(null)" icon="folder">
                    {{ __('Move') }} ({{ count($selected) }})
                </flux:button>

                {{-- <flux:modal.trigger name="new-folder"> --}}
                <flux:button icon="folder-plus" wire:click="openNewFolderModal">{{ __('New Folder') }}</flux:button>
                {{-- </flux:modal.trigger> --}}
            </div>
        </div>
        {{-- Breadcrumbs --}}
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>
                <button wire:click="openFolder(null)">{{ __('Root') }}</button>
            </flux:breadcrumbs.item>
            @foreach ($this->breadcrumbs as $crumb)
                <flux:breadcrumbs.item>
                    <button wire:click="openFolder({{ $crumb->id }})">{{ $crumb->name }}</button>
                </flux:breadcrumbs.item>
            @endforeach
        </flux:breadcrumbs>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            {{-- Folders Table --}}
            <flux:card class="mt-6 sm:col-span-1">
                <flux:heading size="lg" class="mb-4">{{ __('Folders') }}</flux:heading>
                <flux:table :paginate="$this->folders">
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                        {{-- <flux:table.column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection" wire:click="sort('updated_at')">{{ __('Modified') }}</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'updated_by'" :direction="$sortDirection" wire:click="sort('updated_by')">{{ __('Modified by') }}</flux:table.column> --}}
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->folders as $folder)
                            <flux:table.row :key="$folder->id">
                                <flux:table.cell class="group">
                                    <div class="flex items-center justify-between">
                                        <div wire:click="openFolder({{ $folder->id }})" class="flex cursor-pointer items-center space-x-2">
                                            <flux:icon name="folder" size="micro" />
                                            <flux:text class="hover:underline">{{ $folder->name }}</flux:text>
                                        </div>
                                        <div class="group-focus-within:block group-hover:block">
                                            <flux:dropdown>
                                                <flux:button variant="ghost" size="xs" icon="ellipsis-horizontal" inset />
                                                <flux:menu>
                                                    <flux:menu.item icon="pencil" wire:click="openRenameFolderModal({{ $folder->id }})">
                                                        {{ __('Rename') }}
                                                    </flux:menu.item>
                                                    <flux:menu.separator />
                                                    <flux:menu.item variant="danger" icon="trash" wire:click="deleteFolder({{ $folder->id }})" wire:confirm="Are you sure you want to delete this folder?">
                                                        {{ __('Delete') }}
                                                    </flux:menu.item>
                                                </flux:menu>
                                            </flux:dropdown>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                {{-- <flux:table.cell class="whitespace-nowrap">
                                    {{ $folder->updated_at?->diffForHumans() }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $folder->updater?->name ?? '—' }}
                                </flux:table.cell> --}}
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
            {{-- Folders Table --}}
            <flux:card class="mt-6 sm:col-span-2">
                <flux:heading size="lg" class="mb-4">{{ __($folderForm->currentFolderName() . ' Assets') }}</flux:heading>
                <flux:table :paginate="$this->assets">
                    <flux:table.columns>
                        <flux:table.column></flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'original_filename'" :direction="$sortDirection" wire:click="sort('original_filename')">{{ __('Name') }}</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection" wire:click="sort('updated_at')">{{ __('Modified') }}</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'updated_by'" :direction="$sortDirection" wire:click="sort('updated_by')">{{ __('Modified by') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->assets as $asset)
                            <flux:table.row :key="'asset-' . $asset->id">
                                <flux:table.cell class="w-2">
                                    <input type="checkbox" wire:model.live="selected" value="{{ $asset->id }}">
                                </flux:table.cell>
                                <flux:table.cell class="group">
                                    <div class="flex items-center justify-between">
                                        <div wire:click="openAsset({{ $asset->id }})" class="flex cursor-pointer items-center space-x-2">
                                            @if (str_starts_with($asset->mime_type, 'image/'))
                                                <img src="{{ $asset->url }}" alt="{{ $asset->original_filename }}" class="h-6 w-6 rounded object-cover" />
                                            @else
                                                <flux:icon name="folder" size="micro" />
                                            @endif
                                            <flux:text class="hover:underline">{{ substr($asset->original_filename, 0, 15) . (strlen($asset->original_filename) > 15 ? '...' : '') }}</flux:text>
                                        </div>
                                        <div class="group-focus-within:block group-hover:block">
                                            <flux:dropdown>
                                                <flux:button variant="ghost" size="xs" icon="ellipsis-horizontal" inset />
                                                <flux:menu>
                                                    <flux:menu.item wire:click="download({{ $asset->id }})" icon="arrow-down-tray">
                                                        {{ __('Download') }}
                                                    </flux:menu.item>
                                                    <flux:menu.separator />
                                                    <flux:menu.item wire:click="openMoveAssetModal({{ $asset->id }})" icon="folder">
                                                        {{ __('Move') }}
                                                    </flux:menu.item>
                                                    <flux:menu.separator />
                                                    <flux:menu.item wire:click="delete({{ $asset->id }})" icon="trash" variant="danger" wire:confirm="{{ __('Are you sure you want to delete asset ') . $asset->original_filename . '?' }}">
                                                        {{ __('Delete') }}
                                                    </flux:menu.item>
                                                </flux:menu>
                                            </flux:dropdown>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-nowrap">
                                    {{ $asset->updated_at?->diffForHumans() }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $asset->updater?->name ? $asset->updater?->name : $asset->uploader->name }}
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>
    </div>
    <flux:modal name="upload-files">
        @if ($folderForm->currentFolderId)
            <livewire:assets.upload-modal :key="'upload-modal-' . $uploadModalKey" :currentFolderId="$folderForm->currentFolderId" />
        @endif
    </flux:modal>

    <!-- Move Asset Modal -->
    <flux:modal name="move-asset" :closable="false">
        @if (count($selected) > 0)
            <livewire:assets.move-modal :selected="$selected" :currentFolderId="$folderForm->currentFolderId" :key="'move-asset-' . $folderForm->currentFolderId" />
        @endif
    </flux:modal>

    <flux:modal name="new-folder" class="min-h-48 md:w-96">
        <form wire:submit.prevent="createFolder" class="mt-2">
            <flux:input wire:model="folderForm.name" placeholder="{{ __('Folder name') }}" />
            <div class="mt-4 flex justify-end">
                <flux:button type="submit" variant="primary">{{ __('Create') }}</flux:button>
            </div>
        </form>
    </flux:modal>

    <flux:modal name="rename-folder" class="min-h-48 md:w-96">
        <form wire:submit.prevent="renameFolder({{ $folderForm->folderId }})" class="mt-2">
            <div class="space-y-6">
                <flux:heading size="lg">{{ __('Rename Folder') }}</flux:heading>
                <flux:input wire:model="folderForm.name" placeholder="{{ __('Folder name') }}" />
            </div>
            <div class="absolute bottom-4 right-4">
                <div class="mt-4 flex justify-end">
                    <flux:button type="submit" variant="primary">{{ __('Rename') }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
