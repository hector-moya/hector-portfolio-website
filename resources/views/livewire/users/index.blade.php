<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Users</flux:heading>
                <flux:text>Manage user accounts and permissions</flux:text>
            </div>
            @can('create', App\Models\User::class)
                <flux:button wire:navigate href="{{ route('users.create') }}" variant="primary">
                    <flux:icon.plus class="size-5" />
                    Create User
                </flux:button>
            @endcan
        </div>

        {{-- Success Message --}}
        @if (session('message'))
            <flux:callout variant="success">
                {{ session('message') }}
            </flux:callout>
        @endif

        {{-- Search and Filters --}}
        <div class="flex items-center gap-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search users..." class="flex-1" />
            <flux:select wire:model.live="roleFilter" placeholder="All roles" class="w-48">
                <option value="">All roles</option>
                <option value="admin">Admin</option>
                <option value="editor">Editor</option>
                <option value="viewer">Viewer</option>
            </flux:select>
        </div>

        {{-- Users Table --}}
        <div class="overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <table class="w-full">
                <thead class="border-b border-neutral-200 bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-neutral-700 dark:text-neutral-300">Name</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-neutral-700 dark:text-neutral-300">Email</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-neutral-700 dark:text-neutral-300">Role</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-neutral-700 dark:text-neutral-300">Joined</th>
                        <th class="px-6 py-3 text-right text-sm font-medium text-neutral-700 dark:text-neutral-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-200 bg-white dark:divide-neutral-700 dark:bg-neutral-800">
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}" class="hover:bg-neutral-50 dark:hover:bg-neutral-700/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <flux:avatar initials="{{ $user->initials() }}" size="sm" />
                                    <div class="font-medium text-neutral-900 dark:text-neutral-100">
                                        {{ $user->name }}
                                        @if ($user->id === auth()->id())
                                            <flux:badge variant="info" size="sm" class="ml-2">You</flux:badge>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->role === 'admin')
                                    <flux:badge variant="primary">Admin</flux:badge>
                                @elseif ($user->role === 'editor')
                                    <flux:badge variant="success">Editor</flux:badge>
                                @else
                                    <flux:badge variant="neutral">Viewer</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-neutral-600 dark:text-neutral-400">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @can('update', $user)
                                        <flux:button wire:navigate href="{{ route('users.edit', $user) }}" size="sm" variant="ghost">
                                            Edit
                                        </flux:button>
                                    @endcan
                                    @can('delete', $user)
                                        @if ($user->id !== auth()->id())
                                            <flux:button wire:click="delete({{ $user->id }})" wire:confirm="Are you sure you want to delete this user?" size="sm" variant="ghost">
                                                Delete
                                            </flux:button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon.user-group class="size-12 text-neutral-400" />
                                    <div class="text-lg font-medium text-neutral-900 dark:text-neutral-100">No users found</div>
                                    <div class="text-sm text-neutral-500 dark:text-neutral-400">
                                        @if ($search || $roleFilter)
                                            Try adjusting your search or filters
                                        @else
                                            Get started by creating your first user
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div>
            {{ $users->links() }}
        </div>
    </div>
</div>
