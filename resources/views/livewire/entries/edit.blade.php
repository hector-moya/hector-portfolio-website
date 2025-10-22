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
                        <flux:field>
                            <flux:label>
                                {{ $element->label }}
                                @if ($element->is_required)
                                    <flux:badge size="sm" class="ml-2">{{ __('Required') }}</flux:badge>
                                @endif
                            </flux:label>

                            @if ($element->instructions)
                                <flux:description>{{ $element->instructions }}</flux:description>
                            @endif

                            @switch($element->type)
                                @case('text')
                                @case('email')

                                @case('url')
                                    <flux:input type="{{ $element->type }}" wire:model="form.fieldValues.{{ $element->handle }}" :required="$element->is_required" />
                                @break

                                @case('textarea')
                                    <flux:textarea wire:model="form.fieldValues.{{ $element->handle }}" :required="$element->is_required" rows="4" />
                                @break

                                @case('richtext')
                                    <flux:editor wire:model="form.fieldValues.{{ $element->handle }}" :required="$element->is_required" rows="8" />
                                @break

                                @case('number')
                                    <flux:input type="number" step="any" wire:model="form.fieldValues.{{ $element->handle }}" :required="$element->is_required" />
                                @break

                                @case('date')
                                    <flux:input type="date" wire:model="form.fieldValues.{{ $element->handle }}" :required="$element->is_required" />
                                @break

                                @case('time')
                                    <flux:input type="time" wire:model="form.fieldValues.{{ $element->handle }}" :required="$element->is_required" />
                                @break

                                @case('datetime')
                                    <flux:input type="datetime-local" wire:model="form.fieldValues.{{ $element->handle }}" :required="$element->is_required" />
                                @break

                                @case('checkbox')
                                    <flux:checkbox wire:model="form.fieldValues.{{ $element->handle }}" :label="$element->label" />
                                @break

                                @case('select')
                                    <flux:select wire:model="form.fieldValues.{{ $element->handle }}" :required="$element->is_required">
                                        <flux:select.option value="">{{ __('Select an option...') }}</flux:select.option>
                                        @if (isset($element->config['options']))
                                            @foreach ($element->config['options'] as $option)
                                                <flux:select.option value="{{ $option }}">{{ $option }}</flux:select.option>
                                            @endforeach
                                        @endif
                                    </flux:select>
                                @break

                                @case('radio')
                                    <div class="space-y-2">
                                        @if (isset($element->config['options']))
                                            @foreach ($element->config['options'] as $option)
                                                <flux:radio wire:model="form.fieldValues.{{ $element->handle }}" value="{{ $option }}" :label="$option" />
                                            @endforeach
                                        @endif
                                    </div>
                                @break

                                @case('image')
                                @case('file')
                                    <flux:input type="text" wire:model="form.fieldValues.{{ $element->handle }}" :required="$element->is_required" placeholder="{{ __('File path or URL') }}" />
                                    <flux:description>{{ __('File upload functionality coming soon') }}</flux:description>
                                @break
                            @endswitch

                            <flux:error name="form.fieldValues.{{ $element->handle }}" />
                        </flux:field>
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
