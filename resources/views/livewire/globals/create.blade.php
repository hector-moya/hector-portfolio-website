<div>
    <div class="flex flex-col gap-6">
        <div>
            <flux:heading size="2xl">{{ __('New Global Set') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Create a new global variables set.') }}</flux:text>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:card>
                <div class="space-y-4 p-4">
                    <div>
                        <flux:input wire:model="name" label="Name" placeholder="Company Information" />
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

                <div class="flex items-center justify-between p-4">
                    <flux:button wire:navigate href="{{ route('admin.globals.index') }}" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button type="submit" variant="primary">
                        {{ __('Create Set') }}
                    </flux:button>
                </div>
            </flux:card>
        </form>
    </div>
</div>
