<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Edit Entry') }}</flux:heading>
                <flux:text>{{ $entry->collection->name }}</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:modal.trigger name="entry-history">
                    <flux:button icon="clock" variant="ghost" wire:click="$dispatch('open-history', { entryId: {{ $entry->id }} })">
                        {{ __('History') }}
                    </flux:button>
                </flux:modal.trigger>
                <flux:button icon="arrow-uturn-left" :href="route('entries')" wire:navigate>
                    {{ __('Return') }}
                </flux:button>
            </div>
        </div>

        <form wire:submit="save">
            <div class="grid grid-cols-3 gap-6 items-start">

                {{-- ─── Main Column ─── --}}
                <div class="col-span-2 space-y-6">

                    {{-- Title & Slug --}}
                    <flux:card class="space-y-4">
                        <flux:input label="{{ __('Title') }}" wire:model.live.debounce.750ms="form.title" badge="{{ __('Required') }}" />
                        <flux:input label="{{ __('Slug') }}" wire:model="form.slug" badge="{{ __('Required') }}" />
                    </flux:card>

                    {{-- Dynamic Blueprint Fields with Tabs --}}
                    @if ($this->blueprint && $this->blueprint->tabs->isNotEmpty())
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
                                            {{-- Section header (blueprint-style) --}}
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
                                                        <x-dynamic-component
                                                            :component="'entries.fields.' . $field->type"
                                                            :field="$field"
                                                            :form="$form"
                                                        />
                                                    </div>
                                                @endforeach
                                            </div>
                                        </flux:card>
                                    @endforeach
                                </flux:tab.panel>
                            @endforeach
                        </flux:tab.group>
                    @endif

                    {{-- Page Builder --}}
                    <flux:card class="!p-0">
                        <div class="flex items-center gap-2 px-4 py-3">
                            <flux:icon.squares-plus variant="micro" class="shrink-0 text-zinc-400" />
                            <flux:separator vertical class="h-4" />
                            <span class="flex-1 text-sm font-medium">{{ __('Page Builder') }}</span>
                        </div>
                        <flux:separator />
                        <div class="p-5">
                            <livewire:entries.partials.page-builder :entry="$entry" :key="'pb-' . $entry->id" />
                        </div>
                    </flux:card>

                </div>

                {{-- ─── Sidebar ─── --}}
                <div class="col-span-1 space-y-4">

                    {{-- Publish Actions --}}
                    <flux:card class="space-y-4">
                        <flux:heading size="sm">{{ __('Publishing') }}</flux:heading>

                        <flux:input label="{{ __('Collection') }}" :value="$entry->collection->name" disabled />

                        <flux:select label="{{ __('Status') }}" wire:model="form.status" badge="{{ __('Required') }}">
                            <flux:select.option value="draft">{{ __('Draft') }}</flux:select.option>
                            <flux:select.option value="published">{{ __('Published') }}</flux:select.option>
                            <flux:select.option value="archived">{{ __('Archived') }}</flux:select.option>
                        </flux:select>

                        <flux:input label="{{ __('Publish Date') }}" type="datetime-local" wire:model="form.published_at" />

                        <div class="flex justify-end gap-2 pt-2">
                            <flux:button type="button" variant="ghost" :href="route('entries')" wire:navigate>
                                {{ __('Cancel') }}
                            </flux:button>
                            <flux:button type="submit" variant="primary">
                                {{ __('Update Entry') }}
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

                </div>
            </div>
        </form>

        <livewire:entries.history />
    </div>
</div>
