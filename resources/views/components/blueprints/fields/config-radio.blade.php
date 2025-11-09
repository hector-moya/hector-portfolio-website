<div class="space-y-6">
    <flux:heading size="lg">{{ __('Radio Field Settings') }}</flux:heading>
    <div class="flex justify-end">
        <flux:button icon="plus-circle" size="sm" variant="primary" label="{{ __('Add Option') }}" wire:click="addOption" />
    </div>
    <div>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>
                    {{ __('Option Label') }}
                </flux:table.column>
                <flux:table.column>
                    {{ __('Option Value') }}
                </flux:table.column>
                <flux:table.column>
                    {{ __('Remove') }}
                </flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach ($config['options'] as $index => $option)
                    <flux:table.row>
                        <flux:table.cell>
                            <flux:input wire:model.live="form.config.options.{{ $index }}.label" />
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:input wire:model.live="form.config.options.{{ $index }}.value" />
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button variant="danger" icon="trash" wire:click="removeOption({{ $index }})" />
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
