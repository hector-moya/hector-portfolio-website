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
                <flux:card class="space-y-6">
                    <flux:heading size="lg">{{ $this->blueprint->name }}</flux:heading>

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
                    <flux:card>
                        <flux:tab.group>
                            <flux:tabs variant="segmented">
                                @foreach ($this->blueprint->tabs as $tabIndex => $tab)
                                    <flux:tab :name="'tab-' . $tab->id">{{ $tab->name }}</flux:tab>
                                @endforeach
                            </flux:tabs>

                            @foreach ($this->blueprint->tabs as $tab)
                                <flux:tab.panel :name="'tab-' . $tab->id" class="space-y-6 pt-6">
                                    @foreach ($tab->sections as $section)
                                        <div class="space-y-4">
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
                                                    @includeIf('entries.fields.' . $field->type, ['element' => $field])
                                                </div>
                                            @endforeach
                                        </div>

                                        @if (!$loop->last)
                                            <flux:separator />
                                        @endif
                                    @endforeach
                                </flux:tab.panel>
                            @endforeach
                        </flux:tab.group>
                    </flux:card>
                @elseif ($this->blueprint->fields->isNotEmpty())
                    {{-- Fallback for blueprints without tabs --}}
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">{{ __('Content Fields') }}</flux:heading>

                        @foreach ($this->blueprint->fields as $element)
                            <div>
                                @includeIf('entries.fields.' . $element->type, ['element' => $element])
                            </div>
                        @endforeach
                    </flux:card>
                @endif
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
