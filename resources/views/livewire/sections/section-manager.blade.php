<div class="space-y-4">
    {{-- Section list --}}
    @forelse ($sections as $section)
        @php $idx = $loop->index; @endphp
        <div wire:key="section-{{ $section['_id'] }}" class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div x-data="{ open: false }" class="flex flex-col">
                <div class="flex items-center gap-3 p-4">
                    {{-- Reorder buttons --}}
                    <div class="flex flex-col gap-0.5">
                        <button
                            type="button"
                            wire:click="moveSectionUp({{ $idx }})"
                            @class(['opacity-20 cursor-default' => $loop->first, 'hover:text-zinc-600 dark:hover:text-zinc-300' => !$loop->first])
                            class="rounded p-0.5 text-zinc-400 transition-colors"
                            @disabled($loop->first)
                        >
                            <flux:icon.chevron-up class="size-4" />
                        </button>
                        <button
                            type="button"
                            wire:click="moveSectionDown({{ $idx }})"
                            @class(['opacity-20 cursor-default' => $loop->last, 'hover:text-zinc-600 dark:hover:text-zinc-300' => !$loop->last])
                            class="rounded p-0.5 text-zinc-400 transition-colors"
                            @disabled($loop->last)
                        >
                            <flux:icon.chevron-down class="size-4" />
                        </button>
                    </div>

                    {{-- Section type label + preview --}}
                    <div class="flex flex-1 items-center gap-2 min-w-0">
                        <flux:badge size="sm" variant="pill" color="teal">{{ $sectionTypeLabels[$section['type']] ?? $section['type'] }}</flux:badge>
                        @if (!empty($section['data']['title']))
                            <flux:text size="sm" class="truncate text-zinc-500">{{ $section['data']['title'] }}</flux:text>
                        @elseif (!empty($section['data']['content']))
                            <flux:text size="sm" class="truncate text-zinc-400 italic">{{ Str::limit(strip_tags($section['data']['content']), 60) }}</flux:text>
                        @endif
                    </div>

                    {{-- Expand / delete --}}
                    <div class="flex items-center gap-2">
                        <flux:button
                            type="button"
                            variant="ghost"
                            size="sm"
                            icon="trash"
                            wire:click="removeSection({{ $idx }})"
                            wire:confirm="{{ __('Remove this section?') }}"
                            class="text-red-500 hover:text-red-600"
                        />
                        <button
                            type="button"
                            @click="open = !open"
                            class="rounded p-1.5 text-zinc-400 transition-colors hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700 dark:hover:text-zinc-300"
                        >
                            <flux:icon.chevron-down class="size-4 transition-transform" ::class="{ 'rotate-180': open }" />
                        </button>
                    </div>
                </div>

                {{-- Expanded section editor --}}
                <div x-show="open" x-collapse class="border-t border-zinc-200 p-4 dark:border-zinc-700">
                    <x-dynamic-component
                        :component="'fields.editors.' . str_replace('_', '-', $section['type'])"
                        :section="$section"
                        :sectionIndex="$idx"
                    />
                </div>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center gap-2 rounded-lg border-2 border-dashed border-zinc-300 py-10 text-center dark:border-zinc-700">
            <flux:icon.squares-plus class="size-8 text-zinc-400" />
            <flux:text class="text-zinc-500">{{ __('No sections yet. Add one below to build your page.') }}</flux:text>
        </div>
    @endforelse

    {{-- Add Section button --}}
    <div class="pt-1">
        <flux:modal.trigger name="add-section">
            <flux:button type="button" variant="primary" icon="plus">
                {{ __('Add Section') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    {{-- Add Section modal --}}
    <flux:modal name="add-section" class="w-lg">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('Add Section') }}</flux:heading>
                <flux:text>{{ __('Choose a section type to add to your page.') }}</flux:text>
            </div>

            <div class="grid grid-cols-2 gap-3">
                @foreach ($sectionTypes as $type)
                    <flux:card
                        wire:click="set('pendingSectionType', '{{ $type->value }}'); addSection()"
                        class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800"
                    >
                        <div class="flex items-center gap-3">
                            <flux:icon :name="$type->icon()" class="size-5 text-zinc-500" />
                            <div>
                                <flux:text class="font-medium">{{ $type->label() }}</flux:text>
                            </div>
                        </div>
                    </flux:card>
                @endforeach
            </div>
        </div>
    </flux:modal>

    {{-- Asset browser modals (rendered as siblings to avoid nesting issues) --}}
    @foreach ($sections as $section)
        @switch($section['type'])
            @case('hero')
                @php $bgHandle = 'section_' . $section['_id'] . '_bg_image'; @endphp
                <flux:modal name="asset-browser-{{ $bgHandle }}" class="w-3xl">
                    <livewire:assets.browser :fieldHandle="$bgHandle" :key="'sm-' . $bgHandle" />
                </flux:modal>
            @break

            @case('image_text')
                @php $imgHandle = 'section_' . $section['_id'] . '_image'; @endphp
                <flux:modal name="asset-browser-{{ $imgHandle }}" class="w-3xl">
                    <livewire:assets.browser :fieldHandle="$imgHandle" :key="'sm-' . $imgHandle" />
                </flux:modal>
            @break

            @case('gallery')
                @php $gallerySlot = count($section['data']['images'] ?? []); @endphp
                @if ($gallerySlot < 6)
                    @php $slotHandle = 'section_' . $section['_id'] . '_image_' . $gallerySlot; @endphp
                    <flux:modal name="asset-browser-{{ $slotHandle }}" class="w-3xl">
                        <livewire:assets.browser :fieldHandle="$slotHandle" :key="'sm-' . $slotHandle" />
                    </flux:modal>
                @endif
            @break
        @endswitch
    @endforeach
</div>
