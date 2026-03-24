<div>
    <flux:modal name="entry-history" class="w-full max-w-2xl">
        <flux:heading size="lg" class="mb-1">{{ __('Revision History') }}</flux:heading>
        <flux:text class="mb-4 text-sm text-zinc-500">{{ __('All recorded changes for this entry') }}</flux:text>

        @if ($this->revisions->isEmpty())
            <div class="py-8 text-center">
                <flux:icon.clock class="mx-auto size-8 text-zinc-400" variant="outline" />
                <flux:text class="mt-2 text-zinc-500">{{ __('No revision history yet') }}</flux:text>
            </div>
        @else
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach ($this->revisions as $revision)
                    <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <flux:badge size="sm" :color="match($revision->event) { 'created' => 'green', 'updated' => 'blue', 'deleted' => 'red', default => 'zinc' }">
                                    {{ ucfirst($revision->event ?? 'action') }}
                                </flux:badge>
                                <flux:text class="text-sm">{{ $revision->description }}</flux:text>
                            </div>
                            @if ($revision->event === 'updated' && !empty($revision->properties['old']))
                                <flux:button size="xs" variant="ghost" wire:click="restore({{ $revision->id }})" wire:confirm="Restore entry to this version?">
                                    {{ __('Restore') }}
                                </flux:button>
                            @endif
                        </div>

                        <div class="mt-2 text-xs text-zinc-500">
                            {{ $revision->causer?->name ?? __('System') }} &middot; {{ $revision->created_at->diffForHumans() }}
                        </div>

                        @if (!empty($revision->properties['old']) || !empty($revision->properties['new']))
                            <div class="mt-3 grid gap-2 text-xs" style="grid-template-columns: 1fr 1fr">
                                @if (!empty($revision->properties['old']))
                                    <div class="rounded bg-red-50 p-2 dark:bg-red-950/20">
                                        <div class="mb-1 font-medium text-red-600 dark:text-red-400">{{ __('Before') }}</div>
                                        @foreach ($revision->properties['old'] as $key => $value)
                                            <div><span class="text-zinc-500">{{ $key }}:</span> {{ is_array($value) ? json_encode($value) : $value }}</div>
                                        @endforeach
                                    </div>
                                @endif
                                @if (!empty($revision->properties['new']))
                                    <div class="rounded bg-green-50 p-2 dark:bg-green-950/20">
                                        <div class="mb-1 font-medium text-green-600 dark:text-green-400">{{ __('After') }}</div>
                                        @foreach ($revision->properties['new'] as $key => $value)
                                            <div><span class="text-zinc-500">{{ $key }}:</span> {{ is_array($value) ? json_encode($value) : $value }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </flux:modal>
</div>
