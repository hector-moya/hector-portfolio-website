@props([
    'sortBy' => 'display_name',
    'sortDirection' => 'asc',
])

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
                            <div class="group-focus-within:block group-hover:block">
                                @if ($item->type === 'asset')
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="xs" icon="ellipsis-horizontal" inset />
                                        <flux:menu>
                                            <flux:menu.item wire:click="download({{ $item->id }})" icon="arrow-down-tray">
                                                {{ __('Download') }}
                                            </flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item wire:click="openMoveAssetModal({{ $item->id }})" icon="folder">
                                                {{ __('Move') }}
                                            </flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item wire:click="delete({{ $item->id }})" icon="trash" variant="danger" wire:confirm="{{ __('Are you sure you want to delete asset ') . $item->display_name . '?' }}">
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                @else
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="xs" icon="ellipsis-horizontal" inset />
                                        <flux:menu>
                                            <flux:menu.item icon="pencil" wire:click="openRenameFolderModal({{ $item->id }})">
                                                {{ __('Rename') }}
                                            </flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item variant="danger" icon="trash" wire:click="deleteFolder({{ $item->id }})" wire:confirm="Are you sure you want to delete this folder?">
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                @endif
                            </div>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="whitespace-nowrap">
                        {{ $item->updated_at?->diffForHumans() }}
                    </flux:table.cell>
                    <flux:table.cell>
                        {{ $item->updater?->name ? $item->updater?->name : $item->uploader?->name }}
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</flux:card>
