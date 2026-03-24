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
            <flux:heading size="lg">{{ __('Select Collection') }}</flux:heading>
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
                <flux:heading size="lg">{{ $this->blueprint->name }}</flux:heading>
                <flux:card class="space-y-6">
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

                {{-- Dynamic Blueprint Fields with Tabs --}}
                @if ($this->blueprint->tabs->isNotEmpty())
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
                                        <div>
                                            <flux:heading size="md">{{ $section->name }}</flux:heading>
                                            @if ($section->instructions)
                                                <flux:text>{{ $section->instructions }}</flux:text>
                                            @endif
                                        </div>

                                        @foreach ($section->fields as $field)
                                            <x-dynamic-component :component="'entries.fields.' . $field->type" :field="$field" :form="$form" wire:key="field-{{ $field->id }}" />
                                        @endforeach
                                    </flux:card>
                                @endforeach
                            </flux:tab.panel>
                        @endforeach
                    </flux:tab.group>
                @endif
            @endif

            @if ($selectedCollectionId && $this->blueprint)
                {{-- SEO Fields --}}
                <flux:card class="space-y-4">
                    <div>
                        <flux:heading size="md">{{ __('SEO') }}</flux:heading>
                        <flux:text class="text-sm text-zinc-500">{{ __('Search engine optimization settings') }}</flux:text>
                    </div>
                    <flux:input label="{{ __('SEO Title') }}" wire:model="form.seo_title" placeholder="{{ $form->title }}" description="{{ __('Defaults to entry title if left blank. Recommended: 50–60 characters.') }}" />
                    <flux:textarea label="{{ __('SEO Description') }}" wire:model="form.seo_description" rows="3" placeholder="{{ __('A brief summary of this page for search engines...') }}" description="{{ __('Recommended: 120–160 characters.') }}" />
                    <flux:input label="{{ __('OG Image URL') }}" wire:model="form.og_image" placeholder="https://..." description="{{ __('Open Graph image for social media sharing. Recommended: 1200×630px.') }}" />
                </flux:card>
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
