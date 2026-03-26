<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Edit Entry') }}</flux:heading>
                <flux:text>{{ __('Edit the details of the entry below.') }}</flux:text>
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

        {{-- Dynamic Blueprint Fields with Tabs --}}
        @if ($this->blueprint && $this->blueprint->tabs->isNotEmpty())
            <flux:tab.group>
                <flux:tabs variant="segmented">
                    @foreach ($this->blueprint->tabs as $tabIndex => $tab)
                        <flux:tab :name="'tab-' . $tab->id">{{ $tab->name }}</flux:tab>
                    @endforeach
                </flux:tabs>

                @foreach ($this->blueprint->tabs as $tab)
                    <flux:tab.panel :name="'tab-' . $tab->id" class="space-y-6 pt-6">
                        @foreach ($tab->sections as $section)
                            <flux:card class="space-y-4">
                                @if ($section->name)
                                    <div>
                                        <flux:heading size="md">{{ $section->name }}</flux:heading>
                                        @if ($section->instructions)
                                            <flux:text>{{ $section->instructions }}</flux:text>
                                        @endif
                                    </div>
                                @endif

                                @foreach ($section->fields as $field)
                                    <div>
                                        <x-dynamic-component :component="'entries.fields.' . $field->type" :field="$field" :form="$form" wire:key="field-{{ $field->id }}" />
                                    </div>
                                @endforeach
                            </flux:card>
                        @endforeach
                    </flux:tab.panel>
                @endforeach
            </flux:tab.group>
        @endif

        {{-- Page Builder --}}
        <flux:card class="space-y-4">
            <div>
                <flux:heading size="md">{{ __('Page Builder') }}</flux:heading>
                <flux:text>{{ __('Add and arrange sections to build the page layout.') }}</flux:text>
            </div>
            <livewire:entries.partials.page-builder :entry="$entry" :key="'pb-' . $entry->id" />
        </flux:card>

        {{-- SEO Fields --}}
        <flux:card class="space-y-4">
            <div>
                <flux:heading size="md">{{ __('SEO') }}</flux:heading>
                <flux:text class="text-sm text-zinc-500">{{ __('Search engine optimization settings') }}</flux:text>
            </div>

            <flux:input label="{{ __('SEO Title') }}" wire:model="form.seo_title" placeholder="{{ $form->title }}" description="{{ __('Defaults to entry title if left blank. Recommended: 50–60 characters.') }}" />

            <flux:textarea label="{{ __('SEO Description') }}" wire:model="form.seo_description" rows="3" placeholder="{{ __('A brief summary of this page for search engines...') }}" description="{{ __('Recommended: 120–160 characters.') }}" />

            <flux:input label="{{ __('OG Image URL') }}" wire:model="form.og_image" placeholder="https://..." description="{{ __('Open Graph image shown when shared on social media. Recommended: 1200×630px.') }}" />
        </flux:card>

        <form wire:submit="save" class="space-y-6">
            <div class="flex justify-end gap-2">
                <flux:button type="button" variant="ghost" :href="route('entries')" wire:navigate>
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Update Entry') }}
                </flux:button>
            </div>
        </form>

        <livewire:entries.history />
    </div>
</div>
