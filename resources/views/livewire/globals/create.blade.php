<div>
    <div class="mx-auto flex h-full w-full max-w-4xl flex-1 flex-col gap-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('New Global Set') }}</flux:heading>
                <flux:text>{{ __('Create a new global variables set.') }}</flux:text>
            </div>
            <flux:button wire:navigate href="{{ route('admin.globals.index') }}" icon="arrow-uturn-left">
                {{ __('Back') }}
            </flux:button>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:card>
                <div class="space-y-4 p-4">
                    <div>
                        <flux:input wire:model.live.debounce.750ms="name" label="Name" placeholder="Company Information" />
                    </div>

                    <div>
                        <flux:input wire:model="handle" label="Handle" placeholder="company_info" />
                    </div>

                    <div>
                        <flux:select wire:model="blueprint_id" label="Blueprint" placeholder="Select a blueprint...">
                            @foreach ($blueprints as $blueprint)
                                <flux:select.option :value="$blueprint->id">
                                    {{ $blueprint->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                </div>
            </flux:card>

                <div class="flex items-center justify-end gap-3">
                    <flux:button wire:navigate href="{{ route('admin.globals.index') }}" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button type="submit" variant="primary">
                        {{ __('Create Set') }}
                    </flux:button>
                </div>
        </form>
    </div>
</div>
