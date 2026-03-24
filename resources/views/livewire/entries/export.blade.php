<div>
    <flux:modal name="export-entries" class="w-full max-w-md">
        <flux:heading size="lg" class="mb-1">{{ __('Export Entries') }}</flux:heading>
        <flux:text class="mb-4 text-sm text-zinc-500">{{ __('Download entries as a JSON file') }}</flux:text>

        <div class="space-y-4">
            <flux:select label="{{ __('Collection') }}" wire:model="collectionId">
                <flux:select.option value="">{{ __('All Collections') }}</flux:select.option>
                @foreach ($this->collections as $collection)
                    <flux:select.option value="{{ $collection->id }}">{{ $collection->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:checkbox wire:model="includeBlueprint" label="{{ __('Include blueprint reference') }}" />

            <div class="flex justify-end gap-2 pt-2">
                <flux:button variant="ghost" x-on:click="$flux.modal('export-entries').close()">{{ __('Cancel') }}</flux:button>
                <flux:button variant="primary" icon="arrow-down-tray" wire:click="export">{{ __('Download JSON') }}</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
