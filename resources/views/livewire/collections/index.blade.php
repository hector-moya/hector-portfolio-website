<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Collections</flux:heading>
                <flux:text>Manage your content collections</flux:text>
            </div>
            <flux:button wire:navigate href="{{ route('collections.create') }}" variant="primary">
                <flux:icon.plus class="size-5" />
                Create Collection
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
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search collections..." class="flex-1" />
        </div>

        {{-- Collections Table --}}
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="w-full">
                <thead class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-neutral-700 dark:text-neutral-300">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-neutral-700 dark:text-neutral-300">Slug</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-neutral-700 dark:text-neutral-300">Blueprint</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-neutral-700 dark:text-neutral-300">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-neutral-700 dark:text-neutral-300">Entries</th>
                        <th class="px-6 py-3 text-right text-sm font-medium text-neutral-700 dark:text-neutral-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-800">
                    @forelse ($collections as $collection)
                        <tr wire:key="collection-{{ $collection->id }}" class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-neutral-900 dark:text-neutral-100">{{ $collection->name }}</div>
                                @if ($collection->description)
                                    <div class="text-sm text-neutral-500 dark:text-neutral-400">{{ Str::limit($collection->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                                <code class="rounded bg-neutral-100 px-2 py-1 dark:bg-neutral-700">{{ $collection->slug }}</code>
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $collection->blueprint?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($collection->is_active)
                                    <flux:badge variant="success">Active</flux:badge>
                                @else
                                    <flux:badge variant="neutral">Inactive</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $collection->entries_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button wire:navigate href="{{ route('collections.edit', $collection) }}" size="sm" variant="ghost">
                                        Edit
                                    </flux:button>
                                    <flux:button wire:click="delete({{ $collection->id }})" wire:confirm="Are you sure you want to delete this collection?" size="sm" variant="ghost">
                                        Delete
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon.folder class="size-12 text-neutral-400" />
                                    <flux:text>No collections found</flux:text>
                                    <flux:button wire:navigate href="{{ route('collections.create') }}" size="sm" variant="primary">
                                        Create your first collection
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($collections->hasPages())
            <div class="mt-4">
                {{ $collections->links() }}
            </div>
        @endif
    </div>
</div>
