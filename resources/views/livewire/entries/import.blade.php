<div>
    <flux:modal name="import-entries" class="w-full max-w-lg">
        <flux:heading size="lg" class="mb-1">{{ __('Import Entries') }}</flux:heading>
        <flux:text class="mb-4 text-sm text-zinc-500">{{ __('Upload a JSON file exported from this CMS') }}</flux:text>

        @if ($imported)
            <div class="rounded-lg bg-green-50 p-6 text-center dark:bg-green-950/20">
                <flux:icon.check-circle class="mx-auto mb-3 size-10 text-green-500" />
                <flux:heading size="lg">{{ __('Import Complete') }}</flux:heading>
                <flux:text class="mt-2">
                    {{ $importedCount }} {{ __('entries imported') }}
                    @if ($skippedCount > 0)
                        , {{ $skippedCount }} {{ __('skipped') }}
                    @endif
                </flux:text>
                <flux:button class="mt-4" variant="ghost" wire:click="$set('imported', false)">{{ __('Import another file') }}</flux:button>
            </div>
        @else
            <div class="space-y-4">
                <div>
                    <flux:input type="file" wire:model="file" accept=".json" label="{{ __('JSON File') }}" />
                    @error('file') <flux:error>{{ $message }}</flux:error> @enderror
                </div>

                @if (!empty($preview))
                    <div>
                        <flux:text class="mb-2 font-medium">{{ __('Preview (first 5 entries)') }}</flux:text>
                        <div class="max-h-40 overflow-y-auto rounded-lg border border-zinc-200 dark:border-zinc-700">
                            @foreach ($preview as $item)
                                <div class="flex items-center justify-between border-b border-zinc-100 px-3 py-2 last:border-0 dark:border-zinc-700">
                                    <span class="text-sm font-medium">{{ $item['title'] }}</span>
                                    <div class="flex gap-1">
                                        <flux:badge size="sm" color="zinc">{{ $item['collection'] ?? '?' }}</flux:badge>
                                        <flux:badge size="sm" :color="match($item['status'] ?? 'draft') { 'published' => 'green', 'draft' => 'yellow', default => 'zinc' }">
                                            {{ $item['status'] ?? 'draft' }}
                                        </flux:badge>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="flex justify-end gap-2 pt-2">
                    <flux:button variant="ghost" x-on:click="$flux.modal('import-entries').close()">{{ __('Cancel') }}</flux:button>
                    <flux:button variant="primary" icon="arrow-up-tray" wire:click="import" :disabled="!$file">
                        <span wire:loading.remove wire:target="import">{{ __('Import') }}</span>
                        <span wire:loading wire:target="import">{{ __('Importing...') }}</span>
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
