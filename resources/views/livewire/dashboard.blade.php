<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
                <flux:text>{{ __('Welcome back,') }} {{ auth()->user()->name }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:button icon="plus" variant="primary" :href="route('entries.create')" wire:navigate>
                    {{ __('New Entry') }}
                </flux:button>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <flux:card>
                <div class="flex items-center justify-between">
                    <div>
                        <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Total Entries') }}</flux:text>
                        <flux:heading size="xl" class="mt-1">{{ $this->stats['total_entries'] }}</flux:heading>
                    </div>
                    <flux:icon.document-text class="size-8 text-zinc-400" variant="outline" />
                </div>
                <div class="mt-3 flex gap-2">
                    <flux:badge size="sm" color="green">{{ $this->stats['published_entries'] }} {{ __('published') }}</flux:badge>
                    <flux:badge size="sm" color="yellow">{{ $this->stats['draft_entries'] }} {{ __('draft') }}</flux:badge>
                    <flux:badge size="sm" color="zinc">{{ $this->stats['archived_entries'] }} {{ __('archived') }}</flux:badge>
                </div>
            </flux:card>

            <flux:card>
                <div class="flex items-center justify-between">
                    <div>
                        <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Collections') }}</flux:text>
                        <flux:heading size="xl" class="mt-1">{{ $this->stats['total_collections'] }}</flux:heading>
                    </div>
                    <flux:icon.folder class="size-8 text-zinc-400" variant="outline" />
                </div>
                <flux:text class="mt-3 text-sm text-zinc-500">
                    <a href="{{ route('collections.index') }}" wire:navigate class="hover:underline">{{ __('Manage collections') }}</a>
                </flux:text>
            </flux:card>

            <flux:card>
                <div class="flex items-center justify-between">
                    <div>
                        <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Assets') }}</flux:text>
                        <flux:heading size="xl" class="mt-1">{{ $this->stats['total_assets'] }}</flux:heading>
                    </div>
                    <flux:icon.photo class="size-8 text-zinc-400" variant="outline" />
                </div>
                <flux:text class="mt-3 text-sm text-zinc-500">
                    <a href="{{ route('assets.index') }}" wire:navigate class="hover:underline">{{ __('Manage assets') }}</a>
                </flux:text>
            </flux:card>

            <flux:card>
                <div class="flex items-center justify-between">
                    <div>
                        <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('Users') }}</flux:text>
                        <flux:heading size="xl" class="mt-1">{{ $this->stats['total_users'] }}</flux:heading>
                    </div>
                    <flux:icon.user-group class="size-8 text-zinc-400" variant="outline" />
                </div>
                <flux:text class="mt-3 text-sm text-zinc-500">
                    @can('viewAny', App\Models\User::class)
                        <a href="{{ route('users.index') }}" wire:navigate class="hover:underline">{{ __('Manage users') }}</a>
                    @else
                        {{ __('Team members') }}
                    @endcan
                </flux:text>
            </flux:card>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Recent Entries --}}
            <flux:card>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">{{ __('Recent Entries') }}</flux:heading>
                    <flux:button size="sm" variant="ghost" :href="route('entries')" wire:navigate>{{ __('View all') }}</flux:button>
                </div>

                @if ($this->recentEntries->isEmpty())
                    <div class="py-8 text-center">
                        <flux:icon.document-text class="mx-auto size-8 text-zinc-400" variant="outline" />
                        <flux:text class="mt-2 text-zinc-500">{{ __('No entries yet') }}</flux:text>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($this->recentEntries as $entry)
                            <div class="flex items-center justify-between rounded-lg border border-zinc-100 p-3 dark:border-zinc-700">
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('entries.edit', $entry) }}" wire:navigate class="font-medium hover:underline">
                                        {{ $entry->title }}
                                    </a>
                                    <flux:text class="text-sm text-zinc-500">
                                        {{ $entry->collection?->name }} &middot; {{ $entry->created_at->diffForHumans() }}
                                    </flux:text>
                                </div>
                                <flux:badge size="sm" :color="match($entry->status) { 'published' => 'green', 'draft' => 'yellow', 'archived' => 'zinc', default => 'zinc' }">
                                    {{ ucfirst($entry->status) }}
                                </flux:badge>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>

            {{-- Recent Activity --}}
            <flux:card>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">{{ __('Recent Activity') }}</flux:heading>
                    <flux:button size="sm" variant="ghost" :href="route('activity-log.index')" wire:navigate>{{ __('View all') }}</flux:button>
                </div>

                @if ($this->recentActivity->isEmpty())
                    <div class="py-8 text-center">
                        <flux:icon.clock class="mx-auto size-8 text-zinc-400" variant="outline" />
                        <flux:text class="mt-2 text-zinc-500">{{ __('No activity yet') }}</flux:text>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($this->recentActivity as $activity)
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-700">
                                    @if ($activity->event === 'created')
                                        <flux:icon.plus class="size-4 text-green-500" />
                                    @elseif ($activity->event === 'updated')
                                        <flux:icon.pencil class="size-4 text-blue-500" />
                                    @elseif ($activity->event === 'deleted')
                                        <flux:icon.trash class="size-4 text-red-500" />
                                    @else
                                        <flux:icon.bolt class="size-4 text-zinc-500" />
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <flux:text class="text-sm">
                                        <span class="font-medium">{{ $activity->causer?->name ?? __('System') }}</span>
                                        {{ $activity->description }}
                                    </flux:text>
                                    <flux:text class="text-xs text-zinc-500">{{ $activity->created_at->diffForHumans() }}</flux:text>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>
        </div>
    </div>
</x-layouts.app>
