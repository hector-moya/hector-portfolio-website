<div class="space-y-6">
    <flux:heading size="lg">{{ __('Select Field Settings') }}</flux:heading>
    <div>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>
                    {{ __('Option Label') }}
                </flux:table.column>
                <flux:table.column>
                    {{ __('Option Value') }}
                </flux:table.column>
                <flux:table.column class="flex justify-end">
                    <flux:button icon="plus" size="sm" variant="primary" tooltip="{{ __('Add Option') }}" wire:click="addOption" />
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
                        <flux:table.cell class="flex justify-end">
                            <flux:button variant="danger" icon="trash" wire:click="removeOption({{ $index }})" />
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </div>
</div>
