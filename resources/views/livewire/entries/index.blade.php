<div>
    <flux:header>
        <flux:heading size="xl">Entries</flux:heading>

        <flux:separator />

        <flux:button icon="plus" variant="primary" :href="route('entries.create')" wire:navigate>
            New Entry
        </flux:button>
    </flux:header>

    <flux:navbar>
        <flux:navbar.item icon="magnifying-glass">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search entries..." />
        </flux:navbar.item>

        <flux:navbar.item>
            <flux:select wire:model.live="collectionFilter" placeholder="All Collections">
                <option value="">All Collections</option>
                @foreach ($this->collections as $collection)
                    <option value="{{ $collection->id }}">
                        {{ $collection->name }} ({{ $collection->entries_count }})
                    </option>
                @endforeach
            </flux:select>
        </flux:navbar.item>

        <flux:navbar.item>
            <flux:select wire:model.live="statusFilter" placeholder="All Statuses">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </flux:select>
        </flux:navbar.item>
    </flux:navbar>

    @if (!$this->entries)
        <flux:card>
            <div class="flex items-center justify-center py-12">
                <div class="text-center">
                    <flux:icon.document-text variant="outline" class="size-12 mx-auto text-zinc-400 dark:text-zinc-500" />
                    <flux:heading size="lg" class="mt-4">No entries found</flux:heading>
                    <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
                        @if ($search || $collectionFilter || $statusFilter)
                            Try adjusting your search or filter criteria.
                        @else
                            Get started by creating your first entry.
                        @endif
                    </flux:text>
                    @if (!$search && !$collectionFilter && !$statusFilter)
                        <flux:button class="mt-4" variant="primary" :href="route('entries.create')" wire:navigate>
                            Create Entry
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
                    <flux:text>{{ count($selected) }} entries selected</flux:text>
                    <div class="flex gap-2">
                        <flux:button size="sm" wire:click="bulkPublish" wire:confirm="Publish {{ count($selected) }} entries?">
                            <flux:icon.check-circle class="size-4" />
                            Publish
                        </flux:button>
                        <flux:button size="sm" wire:click="bulkUnpublish" wire:confirm="Unpublish {{ count($selected) }} entries?">
                            <flux:icon.x-circle class="size-4" />
                            Unpublish
                        </flux:button>
                        <flux:button size="sm" variant="danger" wire:click="bulkDelete" wire:confirm="Delete {{ count($selected) }} entries?">
                            <flux:icon.trash class="size-4" />
                            Delete
                        </flux:button>
                    </div>
                </div>
            </flux:card>
        @endif

        <flux:table>
            <flux:table.columns>
                <flux:table.column class="w-12">
                    <flux:checkbox wire:model.live="selectAll" />
                </flux:table.column>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Collection</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Author</flux:table.column>
                <flux:table.column>Published</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->entries as $entry)
                    <flux:table.row :key="$entry->id">
                        <flux:table.cell>
                            <flux:checkbox wire:model.live="selected" value="{{ $entry->id }}" />
                        </flux:table.cell>

                        <flux:table.cell>
                            <div>
                                <div class="font-medium">{{ $entry->title }}</div>
                                <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $entry->slug }}</div>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge size="sm" color="zinc">
                                {{ $entry->collection->name }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:badge
                                size="sm"
                                :color="match($entry->status) {
                                    'published' => 'green',
                                    'draft' => 'yellow',
                                    'archived' => 'zinc',
                                }"
                            >
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
                            <div class="flex gap-2">
                                <flux:button size="sm" wire:click="$dispatch('preview-entry', { entryId: {{ $entry->id }} })">
                                    <flux:icon.eye class="size-4" />
                                    Preview
                                </flux:button>

                                <flux:button size="sm" :href="route('entries.edit', $entry)" wire:navigate>
                                    Edit
                                </flux:button>

                                <flux:button
                                    size="sm"
                                    variant="danger"
                                    wire:click="delete({{ $entry->id }})"
                                    wire:confirm="Are you sure you want to delete this entry?"
                                >
                                    Delete
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $this->entries->links() }}
        </div>
    @endif

    {{-- Preview Modal --}}
    <livewire:entries.preview />
</div>
