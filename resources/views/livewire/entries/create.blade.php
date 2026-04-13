<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Create Entry') }}</flux:heading>
                <flux:text>{{ __('Create a new entry for your collection') }}</flux:text>
            </div>
            <flux:button icon="arrow-uturn-left" :href="route('entries')" wire:navigate>
                {{ __('Return') }}
            </flux:button>
        </div>

        <form wire:submit="save">
            <div class="grid grid-cols-3 gap-6 items-start">

                {{-- ─── Main Column ─── --}}
                <div class="col-span-2 space-y-6">

                    {{-- Collection Selection --}}
                    <flux:card class="space-y-4">
                        <flux:heading size="sm">{{ __('Collection') }}</flux:heading>
                        <flux:select wire:model.live="selectedCollectionId" required placeholder="{{ __('Select a collection...') }}">
                            @foreach ($this->collections as $collection)
                                <flux:select.option value="{{ $collection->id }}">
                                    {{ $collection->name }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:card>

                    @if ($selectedCollectionId && $this->blueprint)
                        {{-- Title & Slug --}}
                        <flux:card class="space-y-4">
                            <flux:input label="{{ __('Title') }}" wire:model.live.debounce.750ms="form.title" badge="{{ __('Required') }}" />
                            <flux:input label="{{ __('Slug') }}" wire:model="form.slug" badge="{{ __('Required') }}" />
                        </flux:card>

                        {{-- Dynamic Blueprint Sections with Tabs --}}
                        @if ($this->blueprint->tabs->isNotEmpty())
                            <flux:tab.group>
                                <flux:tabs variant="segmented">
                                    @foreach ($this->blueprint->tabs as $tab)
                                        <flux:tab :name="'tab-' . $tab->id">{{ $tab->name }}</flux:tab>
                                    @endforeach
                                </flux:tabs>

                                @foreach ($this->blueprint->tabs as $tab)
                                    <flux:tab.panel :name="'tab-' . $tab->id" class="space-y-4 pt-6">
                                        @foreach ($tab->sections as $section)
                                            <flux:card class="!p-0">
                                                {{-- Section header --}}
                                                <div class="flex items-center gap-2 px-4 py-3">
                                                    <flux:icon.squares-2x2 variant="micro" class="shrink-0 text-zinc-400" />
                                                    <flux:separator vertical class="h-4" />
                                                    <span class="flex-1 text-sm font-medium">{{ $section->name }}</span>
                                                    @if ($section->handle)
                                                        <flux:badge size="sm" color="zinc" class="font-mono">{{ $section->handle }}</flux:badge>
                                                    @endif
                                                </div>
                                                @if ($section->instructions)
                                                    <div class="border-t border-zinc-100 px-4 py-2 dark:border-zinc-700">
                                                        <flux:text size="sm" class="text-zinc-500">{{ $section->instructions }}</flux:text>
                                                    </div>
                                                @endif
                                                <flux:separator />
                                                <div class="space-y-5 p-5">
                                                    @foreach ($section->fields as $field)
                                                        <div wire:key="field-{{ $field->id }}">
                                                            @php $sectionType = \App\Enums\SectionType::tryFrom($field->type); @endphp
                                                            @if ($sectionType)
                                                                <div class="space-y-2">
                                                                    <div class="flex items-center gap-2">
                                                                        <flux:icon :name="$sectionType->icon()" class="size-4 text-zinc-400" />
                                                                        <flux:text size="sm" class="font-medium">{{ $field->label }}</flux:text>
                                                                        <flux:badge size="sm" variant="pill" color="teal">{{ $sectionType->label() }}</flux:badge>
                                                                    </div>
                                                                    @if ($field->instructions)
                                                                        <flux:text size="sm" class="text-zinc-500">{{ $field->instructions }}</flux:text>
                                                                    @endif
                                                                    <livewire:dynamic-component :is="'sections.' . str_replace('_', '-', $field->type)" :$field wire:model="sectionValues.{{ $field->handle }}" :wire:key="'section-' . $field->id" />
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </flux:card>
                                        @endforeach
                                    </flux:tab.panel>
                                @endforeach
                            </flux:tab.group>
                        @endif
                    @endif

                </div>

                {{-- ─── Sidebar ─── --}}
                <div class="col-span-1 space-y-4">

                    @if ($selectedCollectionId && $this->blueprint)
                        {{-- Publish Actions --}}
                        <flux:card class="space-y-4">
                            <flux:heading size="sm">{{ __('Publishing') }}</flux:heading>

                            <flux:select label="{{ __('Status') }}" wire:model="form.status" badge="{{ __('Required') }}">
                                <flux:select.option value="draft">{{ __('Draft') }}</flux:select.option>
                                <flux:select.option value="published">{{ __('Published') }}</flux:select.option>
                                <flux:select.option value="archived">{{ __('Archived') }}</flux:select.option>
                            </flux:select>

                            <flux:input label="{{ __('Publish Date') }}" wire:model="form.published_at" type="datetime-local" />

                            <div class="flex justify-end gap-2 pt-2">
                                <flux:button type="button" variant="ghost" :href="route('entries')" wire:navigate>
                                    {{ __('Cancel') }}
                                </flux:button>
                                <flux:button type="submit" variant="primary">
                                    {{ __('Create Entry') }}
                                </flux:button>
                            </div>
                        </flux:card>

                        {{-- SEO --}}
                        <flux:card class="space-y-4">
                            <flux:heading size="sm">{{ __('SEO') }}</flux:heading>

                            <flux:input
                                label="{{ __('SEO Title') }}"
                                wire:model="form.seo_title"
                                placeholder="{{ $form->title }}"
                                description="{{ __('Recommended: 50–60 characters.') }}"
                            />

                            <flux:textarea
                                label="{{ __('SEO Description') }}"
                                wire:model="form.seo_description"
                                rows="3"
                                placeholder="{{ __('A brief summary for search engines...') }}"
                                description="{{ __('Recommended: 120–160 characters.') }}"
                            />

                            <flux:input
                                label="{{ __('OG Image URL') }}"
                                wire:model="form.og_image"
                                placeholder="https://..."
                                description="{{ __('Recommended: 1200×630px.') }}"
                            />
                        </flux:card>
                    @endif

                </div>
            </div>
        </form>
    </div>
</div>
