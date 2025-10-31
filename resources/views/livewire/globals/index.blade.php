<div>
    <div class="flex flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="2xl">{{ __('Globals') }}</flux:heading>
                <flux:text class="mt-2">{{ __('Manage site-wide variables organized in sets.') }}</flux:text>
            </div>

            <div class="flex items-center gap-4">
                <flux:button wire:navigate href="{{ route('admin.globals.create') }}" variant="primary">
                    {{ __('New Set') }}
                </flux:button>
            </div>
        </div>

        <flux:card>
            @if($globalSets->count())
                <div class="divide-y divide-zinc-200">
                    @foreach($globalSets as $set)
                        <div class="flex items-center justify-between p-4">
                            <div>
                                <div class="font-medium">{{ $set->name }}</div>
                                <div class="mt-1 text-sm text-zinc-500">
                                    {{ $set->handle }}
                                    @if($set->blueprint)
                                        {{ __('Uses ') . $set->blueprint->name . __(' blueprint') }}
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:button wire:navigate href="{{ route('admin.globals.edit', $set) }}" variant="ghost" size="sm">
                                    {{ __('Edit') }}
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($globalSets->hasPages())
                    <div class="p-4 border-t border-zinc-200">
                        {{ $globalSets->links() }}
                    </div>
                @endif
            @else
                <div class="p-4 text-center">
                    <flux:text>{{ __('No global sets found.') }}</flux:text>
                </div>
            @endif
        </flux:card>
    </div>
</div>
