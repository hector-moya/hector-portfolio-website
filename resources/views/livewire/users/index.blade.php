<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Users') }}</flux:heading>
                <flux:text>{{ __('Manage user accounts and permissions') }}</flux:text>
            </div>
            @can('create', App\Models\User::class)
                <flux:button icon="plus" wire:navigate href="{{ route('users.create') }}" variant="primary">
                    {{ __('Create User') }}
                </flux:button>
            @endcan
        </div>

        {{-- Search and Filters --}}
        <div class="flex items-center gap-4">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search users...') }}" />
            <flux:select wire:model.live="roleFilter" placeholder="{{ __('All roles') }}">
                <flux:select.option value="">{{ __('All roles') }}</flux:select.option>
                <flux:select.option value="admin">{{ __('Admin') }}</flux:select.option>
                <flux:select.option value="editor">{{ __('Editor') }}</flux:select.option>
                <flux:select.option value="viewer">{{ __('Viewer') }}</flux:select.option>
            </flux:select>
        </div>

        {{-- Users Table --}}
        <flux:card>
            <flux:table :paginate="$this->users">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'email'" :direction="$sortDirection" wire:click="sort('email')">{{ __('Email') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'role'" :direction="$sortDirection" wire:click="sort('role')">{{ __('Role') }}</flux:table.column>
                    <flux:table.column>{{ __('Joined') }}</flux:table.column>
                    <flux:table.column class="text-right">{{ __('Actions') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->users as $user)
                        <flux:table.row wire:key="user-{{ $user->id }}">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <flux:avatar initials="{{ $user->initials() }}" size="sm" />
                                    <flux:text>
                                        {{ $user->name }}
                                        @if ($user->id === auth()->id())
                                            <flux:badge variant="info" size="sm" class="ml-2">{{ __('You') }}</flux:badge>
                                        @endif
                                    </flux:text>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $user->email }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ $user->role === 'admin' ? 'blue' : ($user->role === 'editor' ? 'indigo' : 'zinc') }}">
                                    {{ __($user->role) }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $user->created_at->format('d M Y') }}
                            </flux:table.cell>
                            <flux:table.cell class="px-6 py-4 text-right">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        @can('update', $user)
                                            <flux:menu.item icon="pencil" wire:navigate href="{{ route('users.edit', $user) }}">
                                                {{ __('Edit') }}
                                            </flux:menu.item>
                                        @endcan
                                        @can('delete', $user)
                                            @if ($user->id !== auth()->id())
                                                <flux:menu.separator />
                                                <flux:menu.item icon="trash" variant="danger" wire:click="delete({{ $user->id }})" wire:confirm="{{ __('Are you sure you want to delete this user?') }}">
                                                    {{ __('Delete') }}
                                                </flux:menu.item>
                                            @endif
                                        @endcan
                                    </flux:menu>
                                    </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <flux:icon.user-group class="size-12 text-neutral-400" />
                                    <div class="text-lg font-medium text-neutral-900 dark:text-neutral-100">{{ __('No users found') }}</div>
                                    <div class="text-sm text-neutral-500 dark:text-neutral-400">
                                        @if ($search || $roleFilter)
                                            {{ __('Try adjusting your search or filters') }}
                                        @else
                                            {{ __('Get started by creating your first user') }}
                                        @endif
                                    </div>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </flux:card>
    </div>
</div>
