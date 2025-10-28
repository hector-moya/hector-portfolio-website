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
        <x-files-breadcrumbs />

        <div>
            {{-- Files Table --}}
            <x-files-table :actions="true" />
        </div>
    </div>
    <flux:modal name="upload-files">
        <livewire:assets.upload-modal :key="'upload-modal-' . $folderForm->currentFolderId" :currentFolderId="$folderForm->currentFolderId" />
    </flux:modal>

    <!-- Move Asset Modal -->
    <flux:modal name="move-asset" :closable="false">
        @if (count($selected) > 0)
            <livewire:assets.move-modal :selected="$selected" :key="'move-asset-' . $folderForm->currentFolderId" />
        @endif
    </flux:modal>

    <flux:modal name="new-folder" class="min-h-48 md:w-96">
        <x-new-folder />
    </flux:modal>

    <flux:modal name="rename-folder" class="min-h-48 md:w-96">
        <form wire:submit.prevent="renameFolder({{ $folderForm->folderId }})" class="space-y-6">
            <flux:heading size="md">{{ __('Rename Folder') }}</flux:heading>
            <flux:input wire:model="folderForm.name" size="sm" placeholder="{{ __('Folder name') }}" />
            <div class="absolute bottom-4 right-4">
                <div class="mt-4 flex justify-end">
                    <flux:button type="submit" variant="primary">{{ __('Rename') }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
