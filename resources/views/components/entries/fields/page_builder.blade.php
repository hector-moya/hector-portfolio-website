@props([
    'field' => null,
    'form' => null,
    'pageBuilderValues' => [],
    'editingSectionHandle' => '',
    'editingSectionIndex' => -1,
])

@php
    $handle = $field->handle;
    $sections = $pageBuilderValues[$handle] ?? [];
    $allowedTypes = $field->config['allowed_section_types'] ?? array_keys(\App\Support\SectionTypes::labels());
    $sectionTypeLabels = collect(\App\Support\SectionTypes::labels())->only($allowedTypes)->all();

    // Resolve the section currently open in the editor (used by the modal AND the sibling asset-browser modals)
    $esHandle  = $editingSectionHandle;
    $esIdx     = $editingSectionIndex;
    $esSection = ($esHandle !== '' && $esIdx >= 0)
        ? ($pageBuilderValues[$esHandle][$esIdx] ?? null)
        : null;
@endphp

<div class="space-y-3">
    @if ($field->instructions)
        <flux:text size="sm" class="text-zinc-500">{{ $field->instructions }}</flux:text>
    @endif

    {{-- Section list --}}
    @forelse ($sections as $section)
        @php $idx = $loop->index; @endphp
        <div wire:key="pb-{{ $handle }}-{{ $section['_id'] }}" class="rounded-lg border border-zinc-200 bg-white dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center gap-3 p-4">
                {{-- Reorder buttons --}}
                <div class="flex flex-col gap-0.5">
                    <button
                        type="button"
                        wire:click="movePageBuilderSectionUp('{{ $handle }}', {{ $idx }})"
                        @class(['opacity-20 cursor-default' => $loop->first, 'hover:text-zinc-600 dark:hover:text-zinc-300' => !$loop->first])
                        class="rounded p-0.5 text-zinc-400 transition-colors"
                        :disabled="{{ $loop->first ? 'true' : 'false' }}"
                    >
                        <flux:icon.chevron-up class="size-4" />
                    </button>
                    <button
                        type="button"
                        wire:click="movePageBuilderSectionDown('{{ $handle }}', {{ $idx }})"
                        @class(['opacity-20 cursor-default' => $loop->last, 'hover:text-zinc-600 dark:hover:text-zinc-300' => !$loop->last])
                        class="rounded p-0.5 text-zinc-400 transition-colors"
                        :disabled="{{ $loop->last ? 'true' : 'false' }}"
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

                {{-- Edit / delete --}}
                <div class="flex items-center gap-2">
                    <flux:button
                        type="button"
                        variant="ghost"
                        size="sm"
                        icon="pencil-square"
                        wire:click="openSectionEditor('{{ $handle }}', {{ $idx }})"
                    />
                    <flux:button
                        type="button"
                        variant="ghost"
                        size="sm"
                        icon="trash"
                        wire:click="removePageBuilderSection('{{ $handle }}', {{ $idx }})"
                        wire:confirm="{{ __('Remove this section?') }}"
                        class="text-red-500 hover:text-red-600"
                    />
                </div>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center gap-2 rounded-lg border-2 border-dashed border-zinc-300 py-8 text-center dark:border-zinc-700">
            <flux:icon.squares-plus class="size-8 text-zinc-400" />
            <flux:text class="text-zinc-500">{{ __('No sections yet. Add one below to build your page.') }}</flux:text>
        </div>
    @endforelse

    {{-- ─────────────────────────────────────────────────
         Section edit modal
         Note: asset-browser modals are rendered OUTSIDE
         this modal (as siblings below) to avoid nesting.
    ───────────────────────────────────────────────────── --}}
    <flux:modal name="edit-page-builder-section" class="w-2xl max-h-[90vh] overflow-y-auto">
        @if ($esSection)
            <div class="space-y-6">
                <flux:heading size="lg">
                    {{ __('Edit: :type', ['type' => $sectionTypeLabels[$esSection['type']] ?? $esSection['type']]) }}
                </flux:heading>

                @switch($esSection['type'])
                    @case('hero')
                        @php
                            $bgHandle = 'section_' . $esSection['_id'] . '_bg_image';
                            $bgAsset  = ($esSection['data']['bg_image'] ?? null) ? \App\Models\Asset::find($esSection['data']['bg_image']) : null;
                        @endphp
                        <div class="space-y-4">
                            <flux:input label="{{ __('Title') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.title" />
                            <flux:input label="{{ __('Subtitle') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.subtitle" />
                            <flux:textarea label="{{ __('Content') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.content" rows="3" />

                            <div class="space-y-2">
                                <flux:label>{{ __('Background Image') }}</flux:label>
                                @if ($bgAsset)
                                    <div class="group relative w-48 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                                        <img src="{{ $bgAsset->thumbnail_url ?? $bgAsset->url }}" alt="{{ $bgAsset->alt_text }}" class="aspect-video w-full object-cover" />
                                        <button type="button" wire:click="removePageBuilderSectionImage('{{ $esHandle }}', {{ $esIdx }}, 'bg_image')" class="absolute right-2 top-2 rounded-full bg-white p-1 opacity-0 shadow transition-opacity group-hover:opacity-100 dark:bg-zinc-800">
                                            <flux:icon.x-mark class="size-4 text-zinc-500" />
                                        </button>
                                    </div>
                                @endif
                                <flux:modal.trigger name="asset-browser-{{ $bgHandle }}">
                                    <flux:button type="button" variant="ghost" size="sm" icon="photo">
                                        {{ $bgAsset ? __('Change Image') : __('Select Image') }}
                                    </flux:button>
                                </flux:modal.trigger>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <flux:input label="{{ __('Primary CTA Text') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.cta_text" />
                                <flux:input label="{{ __('Primary CTA URL') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.cta_url" placeholder="/" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <flux:input label="{{ __('Secondary CTA Text') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.secondary_cta_text" />
                                <flux:input label="{{ __('Secondary CTA URL') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.secondary_cta_url" placeholder="/" />
                            </div>
                        </div>
                    @break

                    @case('text')
                        <div class="space-y-4">
                            <flux:textarea label="{{ __('Content') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.content" rows="6" />
                            <flux:select label="{{ __('Alignment') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.alignment">
                                <flux:select.option value="left">{{ __('Left') }}</flux:select.option>
                                <flux:select.option value="center">{{ __('Center') }}</flux:select.option>
                                <flux:select.option value="right">{{ __('Right') }}</flux:select.option>
                            </flux:select>
                        </div>
                    @break

                    @case('image_text')
                        @php
                            $imgHandle = 'section_' . $esSection['_id'] . '_image';
                            $imgAsset  = ($esSection['data']['image'] ?? null) ? \App\Models\Asset::find($esSection['data']['image']) : null;
                        @endphp
                        <div class="space-y-4">
                            <flux:input label="{{ __('Title') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.title" />
                            <flux:textarea label="{{ __('Content') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.content" rows="4" />
                            <flux:select label="{{ __('Image Position') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.image_position">
                                <flux:select.option value="left">{{ __('Left') }}</flux:select.option>
                                <flux:select.option value="right">{{ __('Right') }}</flux:select.option>
                            </flux:select>

                            <div class="space-y-2">
                                <flux:label>{{ __('Image') }}</flux:label>
                                @if ($imgAsset)
                                    <div class="group relative w-48 overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                                        <img src="{{ $imgAsset->thumbnail_url ?? $imgAsset->url }}" alt="{{ $imgAsset->alt_text }}" class="aspect-video w-full object-cover" />
                                        <button type="button" wire:click="removePageBuilderSectionImage('{{ $esHandle }}', {{ $esIdx }}, 'image')" class="absolute right-2 top-2 rounded-full bg-white p-1 opacity-0 shadow transition-opacity group-hover:opacity-100 dark:bg-zinc-800">
                                            <flux:icon.x-mark class="size-4 text-zinc-500" />
                                        </button>
                                    </div>
                                @endif
                                <flux:modal.trigger name="asset-browser-{{ $imgHandle }}">
                                    <flux:button type="button" variant="ghost" size="sm" icon="photo">
                                        {{ $imgAsset ? __('Change Image') : __('Select Image') }}
                                    </flux:button>
                                </flux:modal.trigger>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <flux:input label="{{ __('CTA Text') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.cta_text" />
                                <flux:input label="{{ __('CTA URL') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.cta_url" placeholder="/" />
                            </div>
                        </div>
                    @break

                    @case('gallery')
                        @php $gallerySlot = count($esSection['data']['images'] ?? []); @endphp
                        <div class="space-y-4">
                            <flux:input label="{{ __('Title') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.title" />

                            <div>
                                <flux:label>{{ __('Images') }} <span class="text-zinc-400">({{ $gallerySlot }}/6)</span></flux:label>
                                <div class="mt-2 grid grid-cols-3 gap-3 sm:grid-cols-6">
                                    @foreach ($esSection['data']['images'] ?? [] as $imgIdx => $assetId)
                                        @php $galleryAsset = \App\Models\Asset::find($assetId); @endphp
                                        <div class="group relative aspect-square overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                                            @if ($galleryAsset)
                                                <img src="{{ $galleryAsset->thumbnail_url ?? $galleryAsset->url }}" alt="{{ $galleryAsset->alt_text }}" class="h-full w-full object-cover" />
                                            @endif
                                            <button type="button" wire:click="removePageBuilderGalleryImage('{{ $esHandle }}', {{ $esIdx }}, {{ $imgIdx }})" class="absolute right-1 top-1 rounded-full bg-white p-0.5 opacity-0 shadow transition-opacity group-hover:opacity-100 dark:bg-zinc-800">
                                                <flux:icon.x-mark class="size-3 text-zinc-500" />
                                            </button>
                                        </div>
                                    @endforeach

                                    @if ($gallerySlot < 6)
                                        @php $slotHandle = 'section_' . $esSection['_id'] . '_image_' . $gallerySlot; @endphp
                                        <flux:modal.trigger name="asset-browser-{{ $slotHandle }}">
                                            <button type="button" class="flex aspect-square items-center justify-center rounded-lg border-2 border-dashed border-zinc-300 text-zinc-400 transition-colors hover:border-teal-400 hover:text-teal-500 dark:border-zinc-600 dark:hover:border-teal-500">
                                                <flux:icon.plus class="size-6" />
                                            </button>
                                        </flux:modal.trigger>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @break

                    @case('cta')
                        <div class="space-y-4">
                            <flux:input label="{{ __('Title') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.title" />
                            <flux:textarea label="{{ __('Content') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.content" rows="3" />
                            <div class="grid grid-cols-2 gap-4">
                                <flux:input label="{{ __('CTA Text') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.cta_text" />
                                <flux:input label="{{ __('CTA URL') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.cta_url" placeholder="/" />
                            </div>
                        </div>
                    @break

                    @case('features')
                        <div class="space-y-4">
                            <flux:input label="{{ __('Title') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.title" />

                            <div class="space-y-3">
                                <flux:label>{{ __('Feature Items') }}</flux:label>

                                @foreach ($esSection['data']['items'] ?? [] as $itemIdx => $item)
                                    <div wire:key="feature-{{ $esSection['_id'] }}-{{ $itemIdx }}" class="space-y-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                        <div class="flex items-center justify-between">
                                            <flux:text size="sm" class="font-medium">{{ __('Item :number', ['number' => $itemIdx + 1]) }}</flux:text>
                                            <flux:button type="button" variant="ghost" size="sm" icon="trash" wire:click="removePageBuilderFeatureItem('{{ $esHandle }}', {{ $esIdx }}, {{ $itemIdx }})" class="text-red-500" />
                                        </div>
                                        <flux:input label="{{ __('Icon (Heroicon name)') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.items.{{ $itemIdx }}.icon" placeholder="bolt" description="{{ __('Use kebab-case, e.g. light-bulb, arrow-right, check-circle') }}" />
                                        <flux:input label="{{ __('Title') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.items.{{ $itemIdx }}.item_title" />
                                        <flux:textarea label="{{ __('Description') }}" wire:model="pageBuilderValues.{{ $esHandle }}.{{ $esIdx }}.data.items.{{ $itemIdx }}.item_description" rows="2" />
                                    </div>
                                @endforeach

                                <flux:button type="button" variant="ghost" size="sm" icon="plus" wire:click="addPageBuilderFeatureItem('{{ $esHandle }}', {{ $esIdx }})">
                                    {{ __('Add Feature') }}
                                </flux:button>
                            </div>
                        </div>
                    @break
                @endswitch

                <div class="flex justify-end border-t border-zinc-200 pt-4 dark:border-zinc-700">
                    <flux:modal.close>
                        <flux:button variant="primary">{{ __('Done') }}</flux:button>
                    </flux:modal.close>
                </div>
            </div>
        @endif
    </flux:modal>

    {{-- ─────────────────────────────────────────────────────────
         Asset-browser modals — rendered as SIBLINGS of the
         section editor modal, NOT nested inside it.
         Flux resolves modal names globally, so triggers inside
         the section editor modal still open these correctly.
    ─────────────────────────────────────────────────────────── --}}
    @if ($esSection)
        @switch($esSection['type'])
            @case('hero')
                @php $bgHandle = 'section_' . $esSection['_id'] . '_bg_image'; @endphp
                <flux:modal name="asset-browser-{{ $bgHandle }}" class="w-3xl">
                    <livewire:assets.browser :fieldHandle="$bgHandle" :key="'pb-' . $bgHandle" />
                </flux:modal>
            @break

            @case('image_text')
                @php $imgHandle = 'section_' . $esSection['_id'] . '_image'; @endphp
                <flux:modal name="asset-browser-{{ $imgHandle }}" class="w-3xl">
                    <livewire:assets.browser :fieldHandle="$imgHandle" :key="'pb-' . $imgHandle" />
                </flux:modal>
            @break

            @case('gallery')
                @php $gallerySlot = count($esSection['data']['images'] ?? []); @endphp
                @if ($gallerySlot < 6)
                    @php $slotHandle = 'section_' . $esSection['_id'] . '_image_' . $gallerySlot; @endphp
                    <flux:modal name="asset-browser-{{ $slotHandle }}" class="w-3xl">
                        <livewire:assets.browser :fieldHandle="$slotHandle" :key="'pb-' . $slotHandle" />
                    </flux:modal>
                @endif
            @break
        @endswitch
    @endif

    {{-- Add Section button --}}
    <div class="pt-1">
        <flux:modal.trigger name="add-section-{{ $handle }}">
            <flux:button type="button" variant="ghost" size="sm" icon="plus">
                {{ __('Add Section') }}
            </flux:button>
        </flux:modal.trigger>
    </div>

    {{-- Add Section modal --}}
    <flux:modal name="add-section-{{ $handle }}" class="w-sm">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">{{ __('Add Section') }}</flux:heading>
                <flux:text>{{ __('Choose a section type to add to your page.') }}</flux:text>
            </div>

            <flux:select label="{{ __('Section Type') }}" wire:model="pendingSectionTypes.{{ $handle }}">
                <flux:select.option value="" disabled>{{ __('Select a type…') }}</flux:select.option>
                @foreach ($sectionTypeLabels as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button type="button" variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button
                    type="button"
                    variant="primary"
                    wire:click="addPageBuilderSection('{{ $handle }}')"
                >
                    {{ __('Add') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
