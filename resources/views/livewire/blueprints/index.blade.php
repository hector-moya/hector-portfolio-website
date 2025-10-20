<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Blueprints') }}</flux:heading>
                <flux:text>{{ __('Define content structures with custom fields') }}</flux:text>
            </div>
            <flux:button wire:navigate href="{{ route('blueprints.create') }}" variant="primary">
                <flux:icon.plus class="size-5" />
                {{ __('Create Blueprint') }}
            </flux:button>
        </div>

        {{-- Success Message --}}
        @if (session('message'))
            <flux:callout variant="success">
                {{ session('message') }}
            </flux:callout>
        @endif

        {{-- Search --}}
        <div class="flex items-center gap-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search blueprints..." class="flex-1" />
        </div>

        {{-- Blueprints Table --}}
        <flux:card>
            <flux:table :paginate="$this->blueprints">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'slug'" :direction="$sortDirection" wire:click="sort('slug')">{{ __('Slug') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'fields'" :direction="$sortDirection" wire:click="sort('fields')">{{ __('Fields') }}</flux:table.column>
                    <flux:table.column>{{ __('Collections') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">{{ __('Status') }}</flux:table.column>
                    <flux:table.column class="flex justify-end">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->blueprints as $blueprint)
                        <flux:table.row wire:key="blueprint-{{ $blueprint->id }}" class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50">
                            <flux:table.cell class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $blueprint->name }}</div>
                                @if ($blueprint->description)
                                    <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ Str::limit($blueprint->description, 50) }}</div>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                <code class="rounded bg-zinc-100 px-2 py-1 dark:bg-zinc-700">{{ $blueprint->slug }}</code>
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $blueprint->elements_count }} {{ Str::plural('field', $blueprint->elements_count) }}
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $blueprint->collections_count }}
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4">
                                @if ($blueprint->is_active)
                                    <flux:badge variant="success">{{ __('Active') }}</flux:badge>
                                @else
                                    <flux:badge variant="zinc">{{ __('Inactive') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4 text-right">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="pencil" wire:navigate href="{{ route('blueprints.edit', $blueprint) }}">
                                            {{ __('Edit') }}
                                        </flux:menu.item>
                                        @can('delete', $blueprint)
                                            <flux:menu.separator />
                                            <flux:menu.item variant="danger" icon="trash" wire:click="delete({{ $blueprint->id }})" wire:confirm="Are you sure you want to delete this blueprint?">
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        @endcan
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon.document class="size-12 text-zinc-400" />
                                    <flux:text>{{ __('No blueprints found') }}</flux:text>
                                    <flux:button wire:navigate href="{{ route('blueprints.create') }}" size="sm" variant="primary">
                                        {{ __('Create your first blueprint') }}
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
