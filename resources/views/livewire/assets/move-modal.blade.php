<div class="min-w-xs lg:min-w-lg min-h-[32rem]">
    <div class="flex items-center justify-between">
        <flux:breadcrumbs>
            <flux:breadcrumbs.item>
                <button wire:click="openFolder(null)">{{ __('Root') }}</button>
            </flux:breadcrumbs.item>
            @foreach ($this->breadcrumbs as $crumb)
                <flux:breadcrumbs.item wire:click="openFolder({{ $crumb->id }})">
                    {{ $crumb->name }}
                </flux:breadcrumbs.item>
            @endforeach
        </flux:breadcrumbs>
        <flux:modal.trigger name="new-folder-modal">
            <flux:button variant="ghost" icon="folder" size="sm">
                {{ __('Create New Folder') }}
            </flux:button>
        </flux:modal.trigger>
    </div>
    <div class="mt-6 space-y-4">
        <flux:card class="min-h-[24rem]">
            <flux:table class="mt-4">
                <flux:table.columns>
                    <flux:table.column>{{ __('Name') }}</flux:table.column>
                    <flux:table.column sortable>{{ __('Modified') }}</flux:table.column>
                    <flux:table.column>{{ __('Modified by') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->folders['items'] as $item)
                        <flux:table.row :key="$item->id">
                            <flux:table.cell>
                                <div
                                     @if ($item instanceof \App\Models\Folder) wire:click="openFolder({{ $item->id }})"
                            class="flex cursor-pointer items-center space-x-2"
                         @else
                            class="flex items-center space-x-2" @endif>
                                    <flux:icon name="{{ $item instanceof \App\Models\Folder ? 'folder' : 'document' }}" size="micro" />
                                    <flux:text class="{{ $item instanceof \App\Models\Folder ? 'hover:underline' : '' }}">
                                        {{ $item instanceof \App\Models\Folder ? $item->name : $item->original_filename }}
                                    </flux:text>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>{{ $item->updated_at?->diffForHumans() }}</flux:table.cell>
                            <flux:table.cell>{{ $item->updater?->name ?? '—' }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>

    <div class="absolute bottom-4 right-4">
        <div class="flex justify-end gap-2">
            <flux:button wire:click="move" size="sm" variant="primary">
                {{ __('Move Here') }}
            </flux:button>
        </div>
    </div>
    <flux:modal name="new-folder-modal" class="min-h-48 md:w-96">
        <flux:heading size="lg">{{ __('New Folder') }}</flux:heading>
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
