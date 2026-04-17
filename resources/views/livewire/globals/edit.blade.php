<div>
    <div class="flex flex-col gap-6">
        <div>
            <flux:heading size="2xl">{{ __('Edit ') . $globalSet->name }}</flux:heading>
            <flux:text class="mt-2">{{ __('Modify this global variables set.') }}</flux:text>
        </div>

        <form wire:submit="save" class="space-y-6">
            <flux:card>
                <div class="p-4 space-y-4">
                    <flux:input wire:model="form.name" label="Name" placeholder="Company Information" />
                    <flux:input wire:model="form.handle" label="Handle" placeholder="company_info" />
                    <flux:select wire:model.live="form.blueprint_id" label="Blueprint" placeholder="Select a blueprint...">
                        @foreach ($blueprints as $blueprint)
                            <flux:select.option :value="$blueprint->id">
                                {{ $blueprint->name }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
            </flux:card>

            {{-- Dynamic Blueprint Sections --}}
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
                                <flux:card class="p-0!">
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
                                                            <flux:badge size="sm" color="teal">{{ $sectionType->label() }}</flux:badge>
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

            <div class="flex justify-between items-center">
                <flux:button wire:navigate href="{{ route('admin.globals.index') }}" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary">
                    {{ __('Update Set') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
