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
                        <flux:input wire:model="form.name" label="Name" placeholder="Company Information" />
                    </div>

                    <div>
                        <flux:input wire:model="form.handle" label="Handle" placeholder="company_info" />
                    </div>

                    <div>
                        <flux:select wire:model.live="form.blueprint_id" label="Blueprint" placeholder="Select a blueprint...">
                            @foreach ($blueprints as $blueprint)
                                <flux:select.option :value="$blueprint->id">
                                    {{ $blueprint->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>

                    @if($globalSet->blueprint && $globalSet->blueprint->fields->isNotEmpty())
                        <div class="border-t border-zinc-200 pt-4 space-y-4">
                            @foreach($globalSet->blueprint->fields as $field)
                                <div wire:key="field-{{ $field->id }}">
                                    <flux:textarea wire:model="form.variables.{{ $field->handle }}" label="{{ $field->label }}" placeholder="{{ __('Enter a value...') }}" />
                                </div>
                            @endforeach
                        </div>
                    @elseif($globalSet->variables->isNotEmpty())
                        <div class="border-t border-zinc-200 pt-4 space-y-4">
                            @foreach($globalSet->variables as $variable)
                                <div wire:key="variable-{{ $variable->id }}">
                                    <flux:textarea wire:model="form.variables.{{ $variable->handle }}" label="{{ $variable->handle }}" placeholder="{{ __('Enter a value...') }}" />
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
