<div>
    <div class="mx-auto w-full max-w-4xl">
        <div class="flex flex-col gap-6">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">{{ __('Create Blueprint') }}</flux:heading>
                <flux:text>{{ __('Define the structure for your content') }}</flux:text>
            </div>

            <form wire:submit="save" class="flex flex-col space-y-6">
                {{-- Basic Info Card --}}
                <flux:heading size="lg">{{ __('Basic Information') }}</flux:heading>
                <flux:card class="space-y-8">

                    <div class="flex flex-col space-y-8">
                        {{-- Name --}}
                        <flux:input label="{{ __('Name') }}" placeholder="{{ __('Blog Post') }}" badge="{{ __('Required') }}" wire:model.live.debounce.750ms="form.name" />

                        {{-- Slug --}}
                        <flux:input label="{{ __('Slug') }}" placeholder="blog-post" badge="{{ __('Required') }}" wire:model="form.slug" />

                        {{-- Description --}}
                        <flux:textarea label="{{ __('Description') }}" placeholder="Structure for blog posts..." rows="3" badge="{{ __('Optional') }}" wire:model="form.description" />

                        {{-- Status --}}
                        <div class="flex justify-end">
                            <flux:switch label="{{ $form->is_active ? 'Active' : 'Draft' }}" wire:model.live="form.is_active" />
                        </div>
                    </div>
                </flux:card>

                {{-- Fields Card --}}
                <flux:heading size="lg">{{ __('Fields') }}</flux:heading>
                <flux:tab.group>
                    <flux:tabs>
                        @foreach ($tabs as $id => $tab)
                            <flux:tab :name="$id">{{ $tab }}</flux:tab>
                        @endforeach
                        <flux:modal.trigger name="add-tab-modal">
                            <flux:tab icon="plus" action>{{ __('Add tab') }}</flux:tab>
                        </flux:modal.trigger>
                    </flux:tabs>
                    @foreach ($tabs as $id => $tab)
                        <flux:tab.panel :name="$id" class="space-y-6">
                            @foreach ($this->sections as $section)
                                <flux:card class="space-y-6 !p-0 divide-y">
                                    <flux:heading>{{ $section->name }}</flux:heading>
                                    @if (empty($form->elements))
                                        <div class="rounded-lg border-2 border-dashed border-zinc-300 p-8 text-center dark:border-zinc-600">
                                            <flux:text>{{ __('No fields added yet. Click "Add Field" to get started.') }}</flux:text>
                                        </div>
                                    @else
                                        <div class="flex flex-col gap-4">
                                            @foreach ($form->elements as $index => $element)
                                                <flux:card wire:key="element-{{ $index }}">
                                                    <div class="flex items-start gap-4">
                                                        <div class="flex-1 space-y-4">
                                                            <div class="grid grid-cols-2 gap-4">
                                                                {{-- Field Type --}}
                                                                <flux:select label="{{ __('Type') }}" wire:model="form.elements.{{ $index }}.type">
                                                                    @foreach ($this->fieldTypeOptions as $value => $label)
                                                                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                                                    @endforeach
                                                                </flux:select>

                                                                {{-- Field Label --}}
                                                                <flux:input label="{{ __('Label') }}" wire:model.live.debounce.750ms="form.elements.{{ $index }}.label" placeholder="Eg. Post title" />
                                                            </div>

                                                            <div class="grid grid-cols-2 gap-4">
                                                                {{-- Field Title --}}
                                                                <flux:input label="{{ __('Handle') }}" wire:model="form.elements.{{ $index }}.handle" placeholder="post_title" />

                                                                {{-- Field Instructions --}}
                                                                <flux:input label="{{ __('Instructions') }}" wire:model="form.elements.{{ $index }}.instructions" placeholder="Enter the post title" />
                                                            </div>

                                                            {{-- Required Checkbox --}}
                                                            <div class="flex justify-end">
                                                                <flux:switch label="{{ $form->elements[$index]['is_required'] ? 'Required' : 'Optional' }}" wire:model.live="form.elements.{{ $index }}.is_required" />
                                                            </div>

                                                            <div class="mt-4 border-t pt-4">
                                                                <flux:heading size="sm">{{ __('Field Configuration') }}</flux:heading>
                                                                {{-- Dynamic Field Configuration --}}

                                                                @php($type = $form->elements[$index]['type'] ?? 'text')

                                                                @includeIf('blueprints.fields.config-' . $type, ['index' => $index, 'element' => $form->elements[$index]])
                                                            </div>
                                                        </div>

                                                        {{-- Remove Button --}}
                                                        <flux:button type="button" icon="trash" wire:click="removeElement({{ $index }})" size="sm" variant="danger" />
                                                    </div>
                                                </flux:card>
                                            @endforeach
                                        </div>
                                    @endif
                                    <div class="flex justify-center">
                                        <flux:modal.trigger name="select-field-modal">
                                            <flux:button icon="plus" size="sm" variant="primary">
                                                {{ __('Add Field') }}
                                            </flux:button>
                                        </flux:modal.trigger>
                                    </div>
                                </flux:card>
                            @endforeach
                            <flux:card class="max-w-1/2 mx-auto flex justify-center border-2 border-dashed px-16">

                                <flux:button icon="plus" variant="ghost" wire:click="addSection">{{ __('Add Section') }}</flux:button>
                            </flux:card>
                        </flux:tab.panel>
                    @endforeach

                </flux:tab.group>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <flux:button wire:navigate href="{{ route('blueprints.index') }}" type="button" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Create Blueprint') }}
                    </flux:button>
                </div>
            </form>
            <flux:modal name="add-tab-modal">
                <div class="space-y-6">
                    <flux:heading size="lg">{{ __('Add New Tab') }}</flux:heading>
                    <flux:input label="{{ __('Tab Name') }}" placeholder="{{ __('Content') }}" wire:model="newTabName" />
                    <div class="flex justify-end">
                        <flux:button type="button" @click="$flux.modal('add-tab-modal').close()" variant="outline" class="mr-2">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="button" wire:click="addTab">
                            {{ __('Add Tab') }}
                        </flux:button>
                    </div>
                </div>
            </flux:modal>

            <flux:modal name="select-field-modal">
                <div class="space-y-6">
                    <flux:heading size="lg">{{ __('Select Field Type') }}</flux:heading>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($this->fieldTypeMeta as $type)
                            <flux:card :key="$type['value']" wire:click="addElement('{{ $type['value'] }}')" class="cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800">
                                <div class="flex items-center gap-3">
                                    <flux:icon name="{{ $type['icon'] }}" class="h-5 w-5" />
                                    <flux:text>{{ $type['label'] }}</flux:text>
                                </div>
                            </flux:card>
                        @endforeach
                    </div>
                </div>
            </flux:modal>
        </div>
    </div>
</div>
