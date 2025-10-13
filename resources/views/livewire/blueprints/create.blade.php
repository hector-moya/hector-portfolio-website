<div>
    <div class="mx-auto w-full max-w-4xl">
        <div class="flex flex-col gap-6">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">{{ __('Create Blueprint') }}</flux:heading>
                <flux:text>{{ __('Define the structure for your content') }}</flux:text>
            </div>

            <form wire:submit="save" class="flex flex-col gap-6">
                {{-- Basic Info Card --}}
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('Basic Information') }}</flux:heading>

                    <div class="flex flex-col gap-6">
                        {{-- Name --}}
                        <flux:field>
                            <flux:label>{{ __('Name') }} <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model.live="form.name" placeholder="Blog Post" />
                            <flux:error name="form.name" />
                        </flux:field>

                        {{-- Slug --}}
                        <flux:field>
                            <flux:label>{{ __('Slug') }}</flux:label>
                            <flux:input wire:model="form.slug" placeholder="blog-post" />
                            <flux:error name="form.slug" />
                            <flux:text>{{ __('URL-friendly identifier (auto-generated if left blank)') }}</flux:text>
                        </flux:field>

                        {{-- Description --}}
                        <flux:field>
                            <flux:label>{{ __('Description') }}</flux:label>
                            <flux:textarea wire:model="form.description" placeholder="Structure for blog posts..." rows="3" />
                            <flux:error name="form.description" />
                        </flux:field>

                        {{-- Status --}}
                        <flux:field>
                            <flux:checkbox wire:model="form.is_active">
                                <flux:label>{{ __('Active') }}</flux:label>
                            </flux:checkbox>
                        </flux:field>
                    </div>
                </flux:card>

                {{-- Fields Card --}}
                <flux:card>
                    <div class="mb-4 flex items-center justify-between">
                        <flux:heading size="lg">{{ __('Fields') }}</flux:heading>
                        <flux:button icon="plus" wire:click="addElement" size="sm" variant="primary">
                            {{ __('Add Field') }}
                        </flux:button>
                    </div>

                    @if (empty($form->elements))
                        <div class="rounded-lg border-2 border-dashed border-zinc-300 p-8 text-center dark:border-zinc-600">
                            <flux:text>{{ __('No fields added yet. Click "Add Field" to get started.') }}</flux:text>
                        </div>
                    @else
                        <div class="flex flex-col gap-4">
                            @foreach ($form->elements as $index => $element)
                                <div wire:key="element-{{ $index }}" class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-1 space-y-4">
                                            {{-- Type and Label Row --}}
                                            <div class="grid grid-cols-2 gap-4">
                                                <flux:field>
                                                    <flux:label>{{ __('Type') }}</flux:label>
                                                    <flux:select wire:model="form.elements.{{ $index }}.type">
                                                        @foreach ($fieldTypes as $value => $label)
                                                            <option value="{{ $value }}">{{ $label }}</option>
                                                        @endforeach
                                                    </flux:select>
                                                    <flux:error name="form.elements.{{ $index }}.type" />
                                                </flux:field>

                                                <flux:field>
                                                    <flux:label>{{ __('Label') }}</flux:label>
                                                    <flux:input wire:model="form.elements.{{ $index }}.label" placeholder="Title" />
                                                    <flux:error name="form.elements.{{ $index }}.label" />
                                                </flux:field>
                                            </div>

                                            {{-- Handle and Instructions Row --}}
                                            <div class="grid grid-cols-2 gap-4">
                                                <flux:field>
                                                    <flux:label>{{ __('Handle') }}</flux:label>
                                                    <flux:input wire:model="form.elements.{{ $index }}.handle" placeholder="title" />
                                                    <flux:error name="form.elements.{{ $index }}.handle" />
                                                    <flux:text>{{ __('Field identifier for data storage') }}</flux:text>
                                                </flux:field>

                                                <flux:field>
                                                    <flux:label>{{ __('Instructions') }}</flux:label>
                                                    <flux:input wire:model="form.elements.{{ $index }}.instructions" placeholder="Enter the post title" />
                                                    <flux:error name="form.elements.{{ $index }}.instructions" />
                                                </flux:field>
                                            </div>

                                            {{-- Required Checkbox --}}
                                            <flux:field>
                                                <flux:checkbox wire:model="form.elements.{{ $index }}.is_required">
                                                    <flux:label>{{ __('Required field') }}</flux:label>
                                                </flux:checkbox>
                                            </flux:field>
                                        </div>

                                        {{-- Remove Button --}}
                                        <flux:button type="button" wire:click="removeElement({{ $index }})" size="sm" variant="ghost">
                                            <flux:icon.x-mark class="size-4" />
                                        </flux:button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </flux:card>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 border-t border-zinc-200 pt-6 dark:border-zinc-700">
                    <flux:button wire:navigate href="{{ route('blueprints.index') }}" type="button" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Create Blueprint') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
