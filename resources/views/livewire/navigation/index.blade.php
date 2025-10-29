<div>
    <div class="mx-auto w-full max-w-4xl">
        <div class="flex flex-col gap-6">
            {{-- Header --}}
            <div class="flex items-center justify-between gap-4">
                <div>
                    <flux:heading size="xl">{{ __('Navigation') }}</flux:heading>
                    <flux:text>{{ __('Manage your navigation menus') }}</flux:text>
                </div>

                <flux:button :href="route('navigation.create')" wire:navigate>
                    {{ __('New Navigation') }}
                </flux:button>
            </div>

            {{-- Content --}}
            <flux:card>
                {{-- Search --}}
                <div class="mb-6">
                    <flux:input icon="magnifying-glass" type="search" :placeholder="__('Search navigation...')" wire:model.live.debounce.300ms="search" />
                </div>

                {{-- List --}}
                @if($navigations->isEmpty())
                    <flux:card class="rounded-lg border-2 border-dashed border-neutral-300 p-8 text-center dark:border-neutral-600">
                        <flux:text>{{ __('No navigation menus found.') }}</flux:text>
                    </flux:card>
                @else
                    <div class="flex flex-col gap-4">
                        @foreach($navigations as $navigation)
                            <div class="flex items-center justify-between rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                                <div>
                                    <div class="font-medium">{{ $navigation->name }}</div>
                                    <div class="text-sm text-neutral-500 dark:text-neutral-400">{{ $navigation->handle }}</div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <flux:badge variant="{{ $navigation->is_active ? 'success' : 'warning' }}">
                                        {{ $navigation->is_active ? __('Active') : __('Draft') }}
                                    </flux:badge>

                                    <flux:dropdown position="bottom" align="end">
                                        <flux:button variant="ghost" size="sm">
                                            <flux:icon name="more-vertical" />
                                        </flux:button>

                                        <flux:menu>
                                            <flux:menu.item icon="edit" :href="route('navigation.edit', $navigation)" wire:navigate>
                                                {{ __('Edit') }}
                                            </flux:menu.item>

                                            <flux:menu.item icon="trash" wire:click="delete({{ $navigation->id }})" wire:confirm="{{ __('Are you sure you want to delete this navigation menu?') }}">
                                                {{ __('Delete') }}
                                            </flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($navigations->hasPages())
                        <div class="mt-4">
                            {{ $navigations->links() }}
                        </div>
                    @endif
                @endif
            </flux:card>
        </div>
    </div>
</div>
