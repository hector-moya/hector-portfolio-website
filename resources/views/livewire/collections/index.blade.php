<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Collections') }}</flux:heading>
                <flux:text>{{ __('Manage your content collections') }}</flux:text>
            </div>
            <flux:button icon="plus" wire:navigate href="{{ route('collections.create') }}" variant="primary">
                {{ __('Create Collection') }}
            </flux:button>
        </div>

        {{-- Search --}}
        <div class="flex items-center gap-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search collections...') }}" class="flex-1" />
        </div>

        {{-- Collections Table --}}
        <flux:card>
            <flux:table :paginate="$this->collectionModels">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'slug'" :direction="$sortDirection" wire:click="sort('slug')">{{ __('Slug') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'blueprint'" :direction="$sortDirection" wire:click="sort('blueprint')">{{ __('Blueprint') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">{{ __('Status') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'entries'" :direction="$sortDirection" wire:click="sort('entries')">{{ __('Entries') }}</flux:table.column>
                    <flux:table.column class="text-right">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->collectionModels as $collection)
                        <flux:table.row wire:key="collection-{{ $collection->id }}">
                            <flux:table.cell>
                                <div>
                                    <flux:heading level="5">{{ $collection->name }}</flux:heading>
                                    @if ($collection->description)
                                        <flux:text>{{ Str::limit($collection->description, 50) }}</flux:text>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge>{{ $collection->slug }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $collection->blueprint?->name ?? '—' }}
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4">
                                <flux:badge :color="$collection->is_active ? 'green' : 'gray'">
                                    {{ $collection->is_active ? __('Active') : __('Inactive') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $collection->entries_count ?? 0 }}
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4 text-right">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil" wire:navigate href="{{ route('collections.edit', $collection) }}">
                                            {{ __('Edit') }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger" wire:click="delete({{ $collection->id }})" wire:confirm="{{ __('Are you sure you want to delete this collection?') }}">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon.folder class="size-12 text-neutral-400" />
                                    <flux:text>{{ __('No collections found') }}</flux:text>
                                    <flux:button wire:navigate href="{{ route('collections.create') }}" size="sm" variant="primary">
                                        {{ __('Create your first collection') }}
                                    </flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</div>
