<div class="mx-auto max-w-4xl flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Entry AI Wizard') }}</flux:heading>
            <flux:text>{{ __('Describe your entry and let AI pre-fill the content.') }}</flux:text>
        </div>
        <flux:button icon="arrow-uturn-left" wire:navigate href="{{ route('entries') }}">
            {{ __('Cancel') }}
        </flux:button>
    </div>

    {{-- Step indicator --}}
    <div class="flex gap-2">
        <flux:badge :variant="$step === 'describe' ? 'primary' : 'zinc'">1 Describe</flux:badge>
        <flux:badge :variant="$step === 'review' ? 'primary' : 'zinc'">2 Review & Save</flux:badge>
    </div>

    {{-- Step 1: Describe --}}
    @if($step === 'describe')
        <flux:card class="space-y-4">
            <flux:heading size="lg">{{ __('What are you creating?') }}</flux:heading>

            <flux:select
                label="{{ __('Collection') }}"
                wire:model.live="collectionId"
                description="{{ __('The collection this entry belongs to.') }}"
            >
                <flux:select.option value="">{{ __('Select a collection') }}</flux:select.option>
                @foreach($this->collections as $collection)
                    <flux:select.option value="{{ $collection->id }}">{{ $collection->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea
                wire:model="description"
                label="{{ __('Topic Brief') }}"
                rows="4"
                placeholder="A blog post about setting up Laravel with Docker for local development, covering installation, configuration, and common gotchas."
                description="{{ __('Describe the entry topic. The AI will use the blueprint fields to generate matching content.') }}"
            />
        </flux:card>

        <div class="flex justify-end">
            <flux:button
                variant="primary"
                wire:click="generate"
                wire:loading.attr="disabled"
                :disabled="!$collectionId"
                icon="sparkles"
            >
                <span wire:loading.remove wire:target="generate">{{ __('Generate Content') }}</span>
                <span wire:loading wire:target="generate">{{ __('Generating…') }}</span>
            </flux:button>
        </div>
    @endif

    {{-- Step 2: Review --}}
    @if($step === 'review')
        <flux:callout variant="info" icon="information-circle">
            {{ __('Review and edit the generated content before saving. The entry will be saved as a draft.') }}
        </flux:callout>

        <flux:card class="space-y-6">
            <flux:input
                label="{{ __('Title') }}"
                wire:model="generatedTitle"
                badge="{{ __('Required') }}"
            />
            <flux:input
                label="{{ __('Slug') }}"
                wire:model="generatedSlug"
                description="{{ __('URL-friendly identifier') }}"
            />

            <flux:separator />

            @foreach($blueprintFields as $field)
                @php $value = $generatedFields[$field['handle']] ?? null; @endphp

                @if(in_array($field['type'], ['image', 'file', 'page_builder', 'repeater']))
                    <div>
                        <flux:text class="text-sm font-medium">{{ $field['label'] }}</flux:text>
                        <flux:badge variant="zinc" class="mt-1">{{ __('Upload/configure after saving') }}</flux:badge>
                    </div>
                @elseif($field['type'] === 'richtext_block')
                    <div>
                        <flux:label>{{ $field['label'] }}</flux:label>
                        <flux:textarea wire:model="generatedFields.{{ $field['handle'] }}" rows="8" />
                    </div>
                @elseif($field['type'] === 'textarea')
                    <flux:textarea
                        label="{{ $field['label'] }}"
                        wire:model="generatedFields.{{ $field['handle'] }}"
                        rows="4"
                    />
                @elseif($field['type'] === 'toggle')
                    <flux:switch
                        label="{{ $field['label'] }}"
                        wire:model="generatedFields.{{ $field['handle'] }}"
                    />
                @elseif($field['type'] === 'select')
                    <flux:select label="{{ $field['label'] }}" wire:model="generatedFields.{{ $field['handle'] }}">
                        @foreach($field['config']['options'] ?? [] as $option)
                            <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                        @endforeach
                    </flux:select>
                @elseif($field['type'] === 'radio')
                    <div class="space-y-2">
                        <flux:label>{{ $field['label'] }}</flux:label>
                        @foreach($field['config']['options'] ?? [] as $option)
                            <flux:radio
                                label="{{ $option['label'] }}"
                                name="generatedFields.{{ $field['handle'] }}"
                                value="{{ $option['value'] }}"
                                wire:model="generatedFields.{{ $field['handle'] }}"
                            />
                        @endforeach
                    </div>
                @else
                    <flux:input
                        label="{{ $field['label'] }}"
                        wire:model="generatedFields.{{ $field['handle'] }}"
                    />
                @endif
            @endforeach
        </flux:card>

        <div class="flex items-center justify-between">
            <flux:button variant="ghost" wire:click="$set('step', 'describe')">
                {{ __('← Back') }}
            </flux:button>
            <flux:button
                variant="primary"
                wire:click="save"
                wire:loading.attr="disabled"
                icon="check"
            >
                <span wire:loading.remove wire:target="save">{{ __('Save as Draft') }}</span>
                <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
            </flux:button>
        </div>
    @endif
</div>
