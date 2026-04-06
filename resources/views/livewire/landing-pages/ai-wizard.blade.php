<div class="mx-auto max-w-4xl flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Landing Page AI Wizard') }}</flux:heading>
            <flux:text>{{ __('Describe your page and let AI generate the section layout.') }}</flux:text>
        </div>
        <flux:button icon="arrow-uturn-left" wire:navigate href="{{ route('collections.index') }}">
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
            <flux:heading size="lg">{{ __('What is this page for?') }}</flux:heading>

            <flux:input
                label="{{ __('Page Name') }}"
                wire:model.live="name"
                placeholder="{{ __('About Us') }}"
                badge="{{ __('Required') }}"
                description="{{ __('Used as the page title and to generate the URL slug.') }}"
            />
            <flux:error name="name" />

            <flux:input
                label="{{ __('Slug') }}"
                wire:model="slug"
                placeholder="{{ __('about-us') }}"
                description="{{ __('Auto-generated from the name. Must be unique.') }}"
            />
            <flux:error name="slug" />

            <flux:textarea
                wire:model="description"
                label="{{ __('Page Description') }}"
                rows="5"
                placeholder="{{ __('A page introducing our company, team, values, and a call to action to get in touch.') }}"
                description="{{ __('Describe the purpose and content of this page. The more detail, the better the result.') }}"
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
                <span wire:loading.remove wire:target="generate">{{ __('Generate Page') }}</span>
                <span wire:loading wire:target="generate">{{ __('Generating…') }}</span>
            </flux:button>
        </div>
    @endif

    {{-- Step 2: Review --}}
    @if($step === 'review' && !empty($proposal))
        <flux:callout variant="info" icon="information-circle">
            {{ __('Review the proposed sections. You can remove any you don\'t want. Images can be added after saving.') }}
        </flux:callout>

        <flux:card class="p-0! divide-y divide-zinc-200 dark:divide-zinc-700">
            <div class="px-6 py-4">
                <flux:heading level="5">{{ $name }}</flux:heading>
                <flux:text class="text-sm text-zinc-500">{{ $slug }}</flux:text>
            </div>

            @foreach($proposal as $index => $section)
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <flux:badge variant="pill" color="teal">{{ $section['type'] }}</flux:badge>
                        @if(!empty($section['data']['title']))
                            <flux:text class="truncate text-sm">{{ $section['data']['title'] }}</flux:text>
                        @elseif(!empty($section['data']['content']))
                            <flux:text class="truncate text-sm text-zinc-400 italic">
                                {{ \Illuminate\Support\Str::limit(strip_tags($section['data']['content']), 60) }}
                            </flux:text>
                        @endif
                    </div>
                    <flux:button
                        size="sm"
                        variant="ghost"
                        icon="trash"
                        wire:click="removeSection({{ $index }})"
                        class="text-red-500 hover:text-red-600 shrink-0"
                    />
                </div>
            @endforeach
        </flux:card>

        <div class="flex items-center justify-between">
            <flux:button variant="ghost" wire:click="back">
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
