<div class="min-w-xs lg:min-w-lg min-h-[32rem]">
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
        <flux:card class="min-h-[24rem]">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:button size="xs" variant="ghost" icon="arrow-up" wire:click="up" :disabled="!$currentFolderId">
                        {{ __('Up') }}
                    </flux:button>
                    <flux:breadcrumbs>
                        <flux:breadcrumbs.item><button wire:click="up" @disabled(!$currentFolderId)>/{{ __('Root') }}</button></flux:breadcrumbs.item>
                        {{-- Optional: reconstruct breadcrumb from current id if you want here too --}}
                    </flux:breadcrumbs>
                </div>
                <flux:button size="sm" variant="primary" wire:click="move">{{ __('Move Here') }}</flux:button>
            </div>

            <flux:table :paginate="$this->folders" class="mt-4">
                <flux:table.columns>
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    <flux:table.column sortable>{{ __('Modified') }}</flux:table.column>
                    <flux:table.column>{{ __('Modified by') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->folders as $folder)
                        <flux:table.row :key="$folder->id">
                            <flux:table.cell>
                                <button class="inline-flex items-center gap-2" wire:click="enter({{ $folder->id }})">
                                    <flux:icon name="folder" class="h-4 w-4" />
                                    <span class="font-medium">{{ $folder->name }}</span>
                                </button>
                            </flux:table.cell>
                            <flux:table.cell>{{ $folder->updated_at?->diffForHumans() }}</flux:table.cell>
                            <flux:table.cell>{{ $folder->updater?->name ?? '—' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>

        <flux:input wire:model="targetFolder" label="Target Folder" placeholder="Enter destination folder path" size="sm" />
        <p class="text-sm text-gray-500">
            {{ __('Use forward slashes (/) to specify nested folders. Example: /images/2025/october') }}
        </p>
    </div>

    <div class="absolute bottom-4 right-4">
        <div class="flex justify-end gap-2">
            <flux:button wire:click="move" size="sm" variant="primary">
                {{ __('Move Here') }}
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
