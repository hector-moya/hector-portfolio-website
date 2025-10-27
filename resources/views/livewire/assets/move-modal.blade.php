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
                {{ __('New Folder') }}
            </flux:button>
        </flux:modal.trigger>
    </div>
    <div class="mt-6 space-y-4 max-h-96 overflow-y-auto">
        <flux:card class="!p-0">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column></flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'display_name'" :direction="$sortDirection" wire:click="sort('display_name')">{{ __('Name') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'updated_at'" :direction="$sortDirection" wire:click="sort('updated_at')">{{ __('Modified') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'updated_by'" :direction="$sortDirection" wire:click="sort('updated_by')">{{ __('Modified by') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->items as $item)
                        <flux:table.row :key="$item->type . '-' . $item->id">
                            <flux:table.cell class="w-2">
                                @if ($item->type === 'asset')
                                    <flux:checkbox wire:model.live="selected" value="{{ $item->id }}" class="ml-2" />
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="group">
                                <div class="flex items-center justify-between">
                                    <div wire:click="{{ $item->type === 'folder' ? 'openFolder(' . $item->id . ')' : 'openAsset(' . $item->id . ')' }}" class="flex cursor-pointer items-center space-x-2">
                                        @if ($item->type === 'folder')
                                            <flux:icon name="folder" size="micro" />
                                        @elseif(str_starts_with($item->mime_type, 'image/'))
                                            <img src="{{ $item->path }}" alt="{{ $item->display_name }}" class="h-6 w-6 rounded object-cover" />
                                        @else
                                            <flux:icon name="document" size="micro" />
                                        @endif
                                        <flux:text class="hover:underline">
                                            {{ $item->type === 'asset' ? substr($item->display_name, 0, 15) . (strlen($item->display_name) > 15 ? '...' : '') : $item->display_name }}
                                        </flux:text>
                                    </div>
                                    {{-- Add your dropdown menu logic here, checking item->type to show appropriate actions --}}
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="whitespace-nowrap">
                                {{ $item->updated_at?->diffForHumans() }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $item->updater?->name ?? '—' }}
                            </flux:table.cell>
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
