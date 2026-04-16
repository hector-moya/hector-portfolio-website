<div class="mx-auto max-w-4xl flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Blueprint AI Wizard') }}</flux:heading>
            <flux:text>{{ __('Describe your content type and let AI generate the structure.') }}</flux:text>
        </div>
        <flux:button icon="arrow-uturn-left" wire:navigate href="{{ route('blueprints.index') }}">
            {{ __('Cancel') }}
        </flux:button>
    </div>

    {{-- Step indicator --}}
    <div class="flex gap-2">
        <flux:badge :variant="$step === 'describe' ? 'primary' : 'zinc'">1 Describe</flux:badge>
        <flux:badge :variant="$step === 'review' ? 'primary' : 'zinc'">2 Review</flux:badge>
    </div>

    {{-- Step 1: Describe --}}
    @if($step === 'describe')
        <flux:card class="space-y-4">
            <flux:heading size="lg">{{ __('What is this blueprint for?') }}</flux:heading>
            <flux:textarea
                wire:model="description"
                rows="4"
                placeholder="A blog post blueprint with featured image, rich text content, author bio, tags, and SEO fields."
                description="{{ __('Be as specific as you like — the more detail, the better the suggestion.') }}"
            />
            <flux:error name="description" />
        </flux:card>

        <div class="flex justify-end">
            <flux:button
                variant="primary"
                wire:click="generate"
                wire:loading.attr="disabled"
                icon="sparkles"
            >
                <span wire:loading.remove wire:target="generate">{{ __('Generate Blueprint') }}</span>
                <span wire:loading wire:target="generate">{{ __('Generating…') }}</span>
            </flux:button>
        </div>
    @endif

    {{-- Step 2: Review --}}
    @if($step === 'review' && !empty($proposal))
        <flux:callout variant="info" icon="information-circle">
            {{ __('Review the proposed structure. You can remove items — all editing is available after save.') }}
        </flux:callout>

        <flux:card class="p-0! divide-y divide-zinc-200 dark:divide-zinc-700">
            <div class="px-6 py-4">
                <flux:heading size="lg">{{ $proposal['name'] }}</flux:heading>
                <flux:text class="text-sm">{{ $proposal['description'] }}</flux:text>
            </div>

            @foreach($proposal['tabs'] as $tabIndex => $tab)
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <flux:badge>Tab: {{ $tab['name'] }}</flux:badge>
                        <flux:button size="sm" variant="ghost" icon="trash" wire:click="removeTab({{ $tabIndex }})"/>
                    </div>

                    @foreach($tab['sections'] as $sectionIndex => $section)
                        <div class="mt-3 ml-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <flux:text class="text-sm font-semibold">Section: {{ $section['name'] }}</flux:text>
                                <flux:button size="sm" variant="ghost" icon="trash" wire:click="removeSection({{ $tabIndex }}, {{ $sectionIndex }})"/>
                            </div>

                            <div class="ml-4 space-y-1">
                                @foreach($section['fields'] as $fieldIndex => $field)
                                    <div class="flex items-center justify-between py-1">
                                        <div class="flex items-center gap-2">
                                            <flux:badge size="sm" variant="zinc">{{ $field['type'] }}</flux:badge>
                                            <flux:text class="text-sm">{{ $field['label'] ?? $field['name'] ?? '' }}</flux:text>
                                            <flux:text class="text-xs text-zinc-400">{{ $field['handle'] }}</flux:text>
                                            @if(!empty($field['is_required']))
                                                <flux:badge size="sm" variant="primary">required</flux:badge>
                                            @endif
                                        </div>
                                        <flux:button size="sm" variant="ghost" icon="x-mark" wire:click="removeField({{ $tabIndex }}, {{ $sectionIndex }}, {{ $fieldIndex }})"/>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </flux:card>

        <div class="flex items-center justify-between">
            <flux:button variant="ghost" wire:click="$set('step', 'describe')">
                {{ __('← Back') }}
            </flux:button>
            <flux:button variant="primary" wire:click="save" icon="check">
                {{ __('Create Blueprint') }}
            </flux:button>
        </div>
    @endif
</div>
