<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Entries') }}</flux:heading>
                <flux:text>{{ __('Manage your entries') }}</flux:text>
            </div>

            <flux:button icon="plus" variant="primary" :href="route('entries.create')" wire:navigate>
                {{ __('New Entry') }}
            </flux:button>
        </div>

        <div class="flex items-center gap-4">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search entries..." class="flex-grow" />
            <flux:select placeholder="All Collections" wire:model.live="collectionFilter">
                <flux:select.option value="">{{ __('All Collections') }}</flux:select.option>
                @foreach ($this->collections as $collection)
                    <flux:select.option value="{{ $collection->id }}">
                        {{ $collection->name }} ({{ $collection->entries_count }})
                    </flux:select.option>
                @endforeach
            </flux:select>
            <flux:select placeholder="All Statuses" wire:model.live="statusFilter">
                <flux:select.option value="">{{ __('All Statuses') }}</flux:select.option>
                <flux:select.option value="draft">{{ __('Draft') }}</flux:select.option>
                <flux:select.option value="published">{{ __('Published') }}</flux:select.option>
                <flux:select.option value="archived">{{ __('Archived') }}</flux:select.option>
            </flux:select>
        </div>

        @if (!$this->entries)
            <flux:card>
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <flux:icon.document-text variant="outline" class="mx-auto size-12 text-zinc-400 dark:text-zinc-500" />
                        <flux:heading size="lg" class="mt-4">{{ __('No entries found') }}</flux:heading>
                        <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
                            @if ($search || $collectionFilter || $statusFilter)
                                {{ __('Try adjusting your search or filter criteria.') }}
                            @else
                                {{ __('Get started by creating your first entry.') }}
                            @endif
                        </flux:text>
                        @if (!$search && !$collectionFilter && !$statusFilter)
                            <flux:button class="mt-4" variant="primary" :href="route('entries.create')" wire:navigate>
                                {{ __('Create Entry') }}
                            </flux:button>
                        @endif
                    </div>
                </div>
            </flux:card>
        @else
            {{-- Bulk Actions Bar --}}
            @if (count($selected) > 0)
                <flux:card class="mb-4">
                    <div class="flex items-center justify-between">
                        <flux:text>{{ count($selected) }} {{ __('entries selected') }}</flux:text>
                        <div class="flex gap-2">
                            <flux:button size="sm" wire:click="bulkPublish" icon="check-circle" wire:confirm="Publish {{ count($selected) }} {{ __('entries?') }}">
                                {{ __('Publish') }}
                            </flux:button>
                            <flux:button size="sm" wire:click="bulkUnpublish" icon="x-circle" wire:confirm="Unpublish {{ count($selected) }} {{ __('entries?') }}">
                                {{ __('Unpublish') }}
                            </flux:button>
                            <flux:button size="sm" wire:click="bulkArchive" icon="archive-box" wire:confirm="Archive {{ count($selected) }} {{ __('entries?') }}">
                                {{ __('Archive') }}
                            </flux:button>
                            <flux:button size="sm" variant="danger" wire:click="bulkDelete" icon="trash" wire:confirm="Delete {{ count($selected) }} {{ __('entries?') }}">
                                {{ __('Delete') }}
                            </flux:button>
                        </div>
                    </div>
                </flux:card>
            @endif

            <flux:card>
                <flux:table :paginate="$this->entries">
                    <flux:table.columns>
                        <flux:table.column class="w-12">
                            <flux:checkbox wire:model.live="selectAll" />
                        </flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'title'" :direction="$sortDirection" wire:click="sort('title')">{{ __('Title') }}</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'collection_id'" :direction="$sortDirection" wire:click="sort('collection_id')">{{ __('Collection') }}</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sort('status')">{{ __('Status') }}</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'author'" :direction="$sortDirection" wire:click="sort('author')">{{ __('Author') }}</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'published_at'" :direction="$sortDirection" wire:click="sort('published_at')">{{ __('Published') }}</flux:table.column>
                        <flux:table.column>{{ __('Actions') }}</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach ($this->entries as $entry)
                            <flux:table.row :key="$entry->id">
                                <flux:table.cell>
                                    <flux:checkbox wire:model.live="selected" value="{{ $entry->id }}" />
                                </flux:table.cell>

                                <flux:table.cell>
                                    <div>
                                        <flux:heading level="5">{{ $entry->title }}</flux:heading>
                                        <flux:text>{{ $entry->slug }}</flux:text>
                                    </div>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge size="sm" color="zinc">
                                        {{ $entry->collection->name }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:badge size="sm" :color="match($entry->status) { 'published' => 'green', 'draft' => 'yellow', 'archived' => 'zinc', }">
                                        {{ ucfirst($entry->status) }}
                                    </flux:badge>
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $entry->author->name }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    {{ $entry->published_at?->format('M d, Y') ?? '-' }}
                                </flux:table.cell>

                                <flux:table.cell>
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                        <flux:menu>
                                            <flux:menu.item icon="eye" wire:click="$dispatch('preview-entry', { entryId: {{ $entry->id }} })">
                                                {{ __('Preview') }}
                                            </flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item icon="pencil" :href="route('entries.edit', $entry)" wire:navigate>
                                                {{ __('Edit') }}
                                            </flux:menu.item>
                                            <flux:menu.separator />
                                            <flux:menu.item icon="trash" variant="danger" wire:click="delete({{ $entry->id }})" wire:confirm="Are you sure you want to delete this entry?">
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>

            <div class="mt-4">
                {{ $this->entries->links() }}
            </div>
        @endif

        {{-- Preview Modal --}}
        <livewire:entries.preview />
    </div>
</div>
