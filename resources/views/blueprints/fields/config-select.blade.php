{{-- resources/views/blueprints/fields/config-select.blade.php --}}
<div class="space-y-3">
    <flux:heading size="sm">{{ __('Options') }}</flux:heading>

    @foreach (($element['config']['options'] ?? []) as $optIndex => $opt)
        <div class="grid grid-cols-5 gap-3 items-end">
            <div class="col-span-2">
                <flux:input label="{{ __('Value') }}"
                    wire:model="form.elements.{{ $index }}.config.options.{{ $optIndex }}.value" />
            </div>
            <div class="col-span-2">
                <flux:input label="{{ __('Label') }}"
                    wire:model="form.elements.{{ $index }}.config.options.{{ $optIndex }}.label" />
            </div>
            <div class="col-span-1">
                <flux:button size="xs" variant="danger"
                    wire:click="removeOption({{ (int) explode('.', (string) $index)[0] }}, {{ $optIndex }})">
                    {{ __('Remove') }}
                </flux:button>
            </div>
        </div>
    @endforeach

    <flux:button size="xs" icon="plus"
        wire:click="addOption({{ (int) explode('.', (string) $index)[0] }})">
        {{ __('Add option') }}
    </flux:button>
</div>
