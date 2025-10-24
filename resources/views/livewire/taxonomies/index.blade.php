<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Taxonomies') }}</flux:heading>
                <flux:text>{{ __('Organize your content with categories and tags') }}</flux:text>
            </div>
            <flux:button wire:navigate href="{{ route('taxonomies.create') }}" icon="plus" variant="primary">
                {{ __('Create Taxonomy') }}
            </flux:button>
        </div>

        {{-- Search --}}
        <div class="flex items-center gap-4">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search taxonomies..." class="flex-grow" />
        </div>

        {{-- Taxonomies Table --}}
        <flux:card>
            <flux:table :paginate="$this->taxonomies">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'slug'" :direction="$sortDirection" wire:click="sort('slug')">{{ __('Slug') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'terms_count'" :direction="$sortDirection" wire:click="sort('terms_count')">{{ __('Terms') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">{{ __('Status') }}</flux:table.column>
                    <flux:table.column class="flex justify-end">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->taxonomies as $taxonomy)
                        <flux:table.row wire:key="taxonomy-{{ $taxonomy->id }}">
                            <flux:table.cell class="px-6 py-4">
                                <div>
                                    <flux:heading level="5">{{ $taxonomy->name }}</flux:heading>
                                    @if ($taxonomy->description)
                                        <flux:text>{{ Str::limit($taxonomy->description, 50) }}</flux:text>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge>{{ $taxonomy->slug }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $taxonomy->terms_count }} {{ Str::plural('term', $taxonomy->terms_count) }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$taxonomy->is_active ? 'green' : 'zinc'">
                                    {{ $taxonomy->is_active ? 'Active' : 'Inactive' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4 text-right">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil" wire:navigate href="{{ route('taxonomies.edit', $taxonomy) }}">
                                            {{ __('Edit') }}
                                        </flux:menu.item>
                                        @can('delete', $taxonomy)
                                            <flux:menu.separator />
                                            <flux:menu.item variant="danger" icon="trash" wire:click="delete({{ $taxonomy->id }})" wire:confirm="Are you sure you want to delete this taxonomy?">
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        @endcan
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon.tag class="size-12 text-zinc-400" />
                                    <flux:text>{{ __('No taxonomies found') }}</flux:text>
                                    <flux:button wire:navigate href="{{ route('taxonomies.create') }}" size="sm" variant="primary">
                                        {{ __('Create your first taxonomy') }}
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
