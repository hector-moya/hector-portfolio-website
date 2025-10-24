<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Edit Entry') }}</flux:heading>
                <flux:text>{{ __('Edit the details of the entry below.') }}</flux:text>
            </div>

            <flux:button icon="arrow-uturn-left" :href="route('entries')" wire:navigate>
                {{ __('Return') }}
            </flux:button>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:card class="space-y-6">
                {{-- Collection Info (Read-only) --}}
                <flux:input label="{{ __('Collection') }}" :value="$entry->collection->name" disabled description="{{ __('Collection cannot be changed after creation') }}" />

                {{-- Basic Entry Fields --}}
                <flux:input label="{{ __('Title') }}" wire:model.live.debounce.750ms="form.title" badge="{{ __('Required') }}" />

                <flux:input label="{{ __('Slug') }}" wire:model="form.slug" badge="{{ __('Required') }}" />

                <div class="grid grid-cols-2 gap-6">
                    <flux:select label="{{ __('Status') }}" wire:model="form.status" badge="{{ __('Required') }}">
                        <flux:select.option value="draft">{{ __('Draft') }}</flux:select.option>
                        <flux:select.option value="published">{{ __('Published') }}</flux:select.option>
                        <flux:select.option value="archived">{{ __('Archived') }}</flux:select.option>
                    </flux:select>

                    <flux:input label="{{ __('Publish Date') }}" type="datetime-local" wire:model="form.published_at" />
                </div>
            </flux:card>

            {{-- Dynamic Blueprint Fields --}}
            @if ($this->blueprint && $this->blueprint->elements->isNotEmpty())
                <flux:card class="space-y-6">
                    <flux:heading size="lg">{{ __('Content Fields') }}</flux:heading>

                    @foreach ($this->blueprint->elements as $element)
                            <div>
                                @includeIf('entries.fields.' . $element->type, ['element' => $element])
                            </div>
                    @endforeach

                </flux:card>
            @endif

            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" :href="route('entries')" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Update Entry') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
