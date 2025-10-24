<div>
    <div class="mx-auto w-full max-w-4xl">
        <div class="flex flex-col gap-6">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">{{ __('Edit Blueprint') }}</flux:heading>
                <flux:text>{{ __('Update the structure for your content') }}</flux:text>
            </div>

            <form wire:submit="save" class="flex flex-col gap-6">
                {{-- Basic Info Card --}}
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('Basic Information') }}</flux:heading>

                    <div class="flex flex-col gap-6">
                        {{-- Name --}}
                        <flux:input label="{{ __('Name') }}" placeholder="{{ __('Blog Post') }}" badge="{{ __('Required') }}" wire:model.live.debounce.750ms="form.name" />

                        {{-- Slug --}}
                        <flux:input label="{{ __('Slug') }}" placeholder="blog-post" badge="{{ __('Required') }}" wire:model="form.slug" />

                        {{-- Description --}}
                        <flux:input label="{{ __('Description') }}" placeholder="{{ __('Structure for blog posts...') }}" badge="{{ __('Optional') }}" rows="3" wire:model="form.description" />

                        {{-- Status --}}
                        <div class="flex justify-end">
                            <flux:switch label="{{ $form->is_active ? 'Active' : 'Draft' }}" wire:model.live="form.is_active" />
                        </div>
                    </div>
                </flux:card>

                {{-- Fields Card --}}
                <flux:card class="space-y-6">
                    <div class="mb-4 flex items-center justify-between">
                        <flux:heading size="lg">{{ __('Fields') }}</flux:heading>
                    </div>

                    @if (empty($form->elements))
                        <flux:card class="rounded-lg border-2 border-dashed border-neutral-300 p-8 text-center dark:border-neutral-600">
                            <flux:text>{{ __('No fields added yet. Click "Add Field" to get started.') }}</flux:text>
                        </flux:card>
                    @else
                        <div class="flex flex-col gap-4">
                            @foreach ($form->elements as $index => $element)
                                <flux:card wire:key="element-{{ $index }}">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 space-y-4">
                                            {{-- Type and Label Row --}}
                                            <div class="grid grid-cols-2 gap-4">
                                                <flux:select label="{{ __('Type') }}" wire:model="form.elements.{{ $index }}.type">
                                                    @foreach ($this->fieldTypeOptions as $value => $label)
                                                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                                                    @endforeach
                                                </flux:select>

                                                <flux:input label="{{ __('Label') }}" wire:model.live.debounce.750ms="form.elements.{{ $index }}.label" placeholder="Eg. Post title" />
                                            </div>

                                            {{-- Handle and Instructions Row --}}
                                            <div class="grid grid-cols-2 gap-4">
                                                <flux:input label="{{ __('Handle') }}" wire:model="form.elements.{{ $index }}.handle" placeholder="post_title" />

                                                <flux:input label="{{ __('Instructions') }}" wire:model="form.elements.{{ $index }}.instructions" placeholder="Enter the post title" />
                                            </div>

                                            {{-- Required Checkbox --}}
                                            <div class="flex justify-end">
                                                <flux:switch label="{{ $form->elements[$index]['is_required'] ? __('Required') : __('Optional') }}" wire:model.live="form.elements.{{ $index }}.is_required" />
                                            </div>

                                            <div class="mt-4 border-t pt-4">
                                                <flux:heading size="sm">{{ __('Field Configuration') }}</flux:heading>
                                                {{-- Dynamic Field Configuration --}}

                                                @php($type = $form->elements[$index]['type'] ?? 'text')

                                                @includeIf('blueprints.fields.config-' . $type, [
                                                    'index' => $index,
                                                    'element' => $form->elements[$index],
                                                ])
                                            </div>
                                        </div>

                                        {{-- Remove Button --}}
                                        <flux:button icon="trash" type="button" wire:click="removeElement({{ $index }})" size="sm" variant="danger" />
                                    </div>
                                </flux:card>
                            @endforeach
                        </div>
                    @endif
                    <div class="flex justify-center">
                        <flux:modal.trigger name="select-field-modal">
                            <flux:button icon="plus" type="button" size="sm" variant="primary">
                                {{ __('Add Field') }}
                            </flux:button>
                        </flux:modal.trigger>
                    </div>
                </flux:card>

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

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <flux:button wire:navigate href="{{ route('blueprints.index') }}" type="button" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Update Blueprint') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
