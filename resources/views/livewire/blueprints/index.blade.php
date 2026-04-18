<div>
    <div class="mx-auto flex h-full w-full max-w-4xl flex-1 flex-col gap-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Blueprints') }}</flux:heading>
                <flux:text>{{ __('Define content structures with custom fields') }}</flux:text>
            </div>
            <div class="flex items-center gap-2">
                <flux:button icon="sparkles" wire:navigate href="{{ route('blueprints.ai-wizard') }}" variant="ghost">
                    {{ __('Create with AI') }}
                </flux:button>
                <flux:button wire:navigate wire:click="createBlueprint" icon="plus" variant="primary">
                    {{ __('Create Blueprint') }}
                </flux:button>
            </div>
        </div>

        {{-- Search --}}
        <div class="flex items-center gap-4">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search blueprints..." class="grow" />
        </div>

        {{-- Blueprints Table --}}
        <flux:card>
            <flux:table :paginate="$this->blueprints">
                <flux:table.columns>
                    <flux:table.column class="{{ $this->canReorder() ? 'w-8' : 'hidden' }}"></flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'slug'" :direction="$sortDirection" wire:click="sort('slug')">{{ __('Slug') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'fields'" :direction="$sortDirection" wire:click="sort('fields')">{{ __('Fields') }}</flux:table.column>
                    <flux:table.column>{{ __('Collections') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">{{ __('Status') }}</flux:table.column>
                    <flux:table.column class="flex justify-end">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows wire:sort="reorder">
                    @forelse ($this->blueprints as $blueprint)
                        <flux:table.row wire:key="blueprint-{{ $blueprint->id }}" wire:sort:item="{{ $blueprint->id }}">
                            <flux:table.cell class="{{ $this->canReorder() ? 'w-8 px-2' : 'hidden' }}">
                                <flux:icon.grip-vertical wire:sort:handle class="size-4 cursor-move text-zinc-400" />
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4">
                                <div>
                                    <flux:heading level="5">{{ $blueprint->name }}</flux:heading>
                                    @if ($blueprint->description)
                                        <flux:text>{{ Str::limit($blueprint->description, 50) }}</flux:text>
                                    @endif
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge>{{ $blueprint->slug }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $blueprint->fields_count }} {{ Str::plural('field', $blueprint->fields_count) }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $blueprint->collections_count }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" :color="$blueprint->is_active ? 'green' : 'zinc'">
                                    {{ $blueprint->is_active ? 'Active' : 'Inactive' }}
                                </flux:badge>
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
                            <flux:table.cell colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon.document class="size-12 text-zinc-400" />
                                    <flux:text>{{ __('No blueprints found') }}</flux:text>
                                    <flux:button wire:click="createBlueprint" size="sm" variant="primary">
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
