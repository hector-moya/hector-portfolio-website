<div>
    <div class="flex flex-col gap-6">
        <div>
            <flux:heading size="2xl">{{ __('Edit ') . $globalSet->name }}</flux:heading>
            <flux:text class="mt-2">{{ __('Modify this global variables set.') }}</flux:text>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:card>
                <div class="p-4 space-y-4">
                    <div>
                        <flux:input wire:model="name" label="Name" placeholder="Company Information" />
                    </div>

                    <div>
                        <flux:input wire:model="handle" label="Handle" placeholder="company_info" />
                    </div>

                    <div>
                        <flux:select wire:model.live="blueprint_id" label="Blueprint" placeholder="Select a blueprint...">
                            @foreach ($blueprints as $blueprint)
                                <flux:select.option :value="$blueprint->id">
                                    {{ $blueprint->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    @if($blueprint_id)
                        <div class="border-t border-zinc-200 pt-4">
                            @foreach($globalSet->blueprint->elements as $element)
                                @include('entries.fields.' . $element->type, [
                                    'element' => $element,
                                    'form' => $this,
                                ])
                            @endforeach
                        </div>
                    @else
                        <div class="border-t border-zinc-200 pt-4">
                            @foreach($globalSet->variables as $variable)
                                <div class="mb-4">
                                    <flux:textarea wire:model="variables.{{ $variable->handle }}" label="{{ $variable->handle }}" placeholder="Enter a value..." />
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="p-4 flex justify-between items-center">
                    <flux:button wire:navigate href="{{ route('admin.globals.index') }}" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button type="submit" variant="primary">
                        {{ __('Update Set') }}
                    </flux:button>
                </div>
            </flux:card>
        </form>
    </div>
</div>
