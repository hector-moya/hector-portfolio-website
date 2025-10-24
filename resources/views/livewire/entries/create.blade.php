<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Create Entry') }}</flux:heading>
                <flux:text>{{ __('Create a new entry for your collection') }}</flux:text>
            </div>
            <flux:button icon="arrow-uturn-left" :href="route('entries')" wire:navigate>
                {{ __('Return') }}
            </flux:button>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:card>
                {{-- Collection Selection --}}
                <flux:select label="{{ __('Collection') }}" wire:model.live="selectedCollectionId" required>
                    <flux:select.option value="">{{ __('Select a collection...') }}</flux:select.option>
                    @foreach ($this->collections as $collection)
                        <flux:select.option value="{{ $collection->id }}">
                            {{ $collection->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </flux:card>

            @if ($selectedCollectionId && $this->blueprint)
                <flux:card class="space-y-6">
                    {{-- <flux:separator text="{{ $this->blueprint->name }}" /> --}}
                    <flux:heading size="lg">{{ $this->blueprint->name }}</flux:heading>
                    {{-- Basic Entry Fields --}}
                    <flux:input label="{{ __('Title') }}" wire:model.live.debounce.750ms="form.title" badge="{{ __('Required') }}" description="{{ __('The title of the entry') }}" />

                    <flux:input label="{{ __('Slug') }}" wire:model="form.slug" badge="{{ __('Required') }}" description="{{ __('URL-friendly version of the title') }}" />

                    <div class="grid grid-cols-2 gap-6">
                        <flux:select label="{{ __('Status') }}" wire:model="form.status" badge="{{ __('Required') }}">
                            <flux:select.option value="draft">{{ __('Draft') }}</flux:select.option>
                            <flux:select.option value="published">{{ __('Published') }}</flux:select.option>
                            <flux:select.option value="archived">{{ __('Archived') }}</flux:select.option>
                        </flux:select>

                        <flux:input label="{{ __('Publish Date') }}" wire:model="form.published_at" type="datetime-local" />
                    </div>
                </flux:card>

                {{-- Dynamic Blueprint Fields --}}
                @if ($this->blueprint->elements->isNotEmpty())

                    <flux:card class="space-y-6">
                        <flux:heading size="lg">{{ __('Content Fields') }}</flux:heading>
                        {{-- <flux:separator text="{{ __('Content Fields') }}" /> --}}

                        @foreach ($this->blueprint->elements as $element)
                            <div>
                                @includeIf('entries.fields.' . $element->type, ['element' => $element])
                            </div>
                        @endforeach
                    </flux:card>
                @endif
            @endif

            @if ($selectedCollectionId && $this->blueprint)
                <div class="flex justify-end gap-2">
                    <flux:button type="button" variant="ghost" :href="route('entries')" wire:navigate>
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Create Entry') }}
                    </flux:button>
                </div>
            @endif
        </form>
    </div>
</div>
