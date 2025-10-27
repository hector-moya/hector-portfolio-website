<div class="min-w-xs lg:min-w-lg min-h-[32rem]">
    <div class="flex items-center justify-between">
        <x-files-breadcrumbs />
        <flux:button icon="folder-plus" wire:click="newFolderModal">{{ __('New Folder') }}</flux:button>
    </div>
    <div class="mt-6 max-h-96 space-y-4 overflow-y-auto">
        <x-files-table />
    </div>

    <div class="absolute bottom-4 right-4">
        <div class="flex justify-end gap-2">
            <flux:button wire:click="move" size="sm" variant="primary">
                {{ __('Move Here') }}
            </flux:button>
        </div>
    </div>
    <flux:modal name="new-folder-modal" class="min-h-48 md:w-96">
        <x-new-folder />
    </flux:modal>
</div>
