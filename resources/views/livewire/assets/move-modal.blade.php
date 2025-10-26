<div class="min-h-[32rem] min-w-xs lg:min-w-lg">
    <div class="flex items-center justify-between">
        <flux:breadcrumbs>
            @foreach ($currentFolderPath as $folder)
                <flux:breadcrumbs.item>
                    {{ $folder }}
                </flux:breadcrumbs.item>
            @endforeach
            <flux:breadcrumbs.item>
                {{ $form->original_filename }}
            </flux:breadcrumbs.item>
        </flux:breadcrumbs>
        <flux:modal.trigger name="new-folder-modal">
            <flux:button variant="ghost" icon="folder" size="sm">
                {{ __('Create New Folder') }}
            </flux:button>
        </flux:modal.trigger>
    </div>
    <div class="mt-6 space-y-4">
        <flux:input wire:model="targetFolder" label="Target Folder" placeholder="Enter destination folder path" size="sm" />
        <p class="text-sm text-gray-500">
            {{ __('Use forward slashes (/) to specify nested folders. Example: /images/2025/october') }}
        </p>
    </div>

    <div class="absolute bottom-4 right-4">
        <div class="flex justify-end gap-2">
            <flux:button wire:click="move" size="sm" variant="primary">
                {{ __('Move Asset') }}
            </flux:button>
        </div>
    </div>
    <flux:modal name="new-folder-modal" class="min-h-48 md:w-96">
        <flux:heading size="lg">{{ __('Create New Folder') }}</flux:heading>
        <form wire:submit.prevent="createNewFolder" class="mt-6">
            <flux:input wire:model="newFolderName" placeholder="Enter folder name" />
            <div class="absolute bottom-4 right-4">
                <div class="flex justify-end gap-2">
                    <flux:button type="submit" variant="primary" icon="folder-plus" size="sm">
                        {{ __('Create') }}
                    </flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
