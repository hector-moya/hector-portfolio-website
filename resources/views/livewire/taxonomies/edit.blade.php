<div>
    <div class="mx-auto flex h-full w-full max-w-4xl flex-1 flex-col gap-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">{{ __('Edit Taxonomy') }}</flux:heading>
                <flux:text>{{ __('Update your content organization structure') }}</flux:text>
            </div>
            <flux:button wire:click="delete" type="button" variant="danger" wire:confirm="Are you sure you want to delete this taxonomy?">
                {{ __('Delete Taxonomy') }}
            </flux:button>
        </div>

        <form wire:submit="save" class="flex flex-col gap-6">
            {{-- Basic Info Card --}}
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Basic Information') }}</flux:heading>

                <div class="flex flex-col gap-6">
                    {{-- Name --}}
                    <flux:input label="{{ __('Name') }}" placeholder="{{ __('Categories') }}" badge="{{ __('Required') }}" wire:model.live.debounce.750ms="form.name" />

                    {{-- Slug --}}
                    <flux:input label="{{ __('Slug') }}" placeholder="categories" badge="{{ __('Required') }}" wire:model="form.handle" />

                </div>
            </flux:card>

            {{-- Terms Card --}}
            <flux:card>
                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">{{ __('Terms') }}</flux:heading>
                    <flux:button size="sm" wire:click="addTerm" icon="plus" variant="primary">
                        {{ __('Add Term') }}
                    </flux:button>
                </div>

                @if (empty($this->form->terms))
                    <div class="rounded-lg border-2 border-dashed border-zinc-300 p-8 text-center dark:border-zinc-600">
                        <flux:text>{{ __('No terms added yet. Click "Add Term" to get started.') }}</flux:text>
                    </div>
                @else
                    <div class="space-y-4">
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('No') }}</flux:table.column>
                                <flux:table.column>{{ __('Name') }}</flux:table.column>
                                <flux:table.column>{{ __('Slug') }}</flux:table.column>
                                <flux:table.column></flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach ($this->form->terms as $term)
                                    <flux:table.row wire:key="term-row-{{ $term['id'] }}">
                                        <flux:table.cell>
                                            <flux:badge>{{ $loop->iteration }}</flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:input wire:model="form.terms.{{ $loop->index }}.name" />
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:input wire:model="form.terms.{{ $loop->index }}.slug" />
                                        </flux:table.cell>
                                        <flux:table.cell>
                                            <flux:select wire:model="form.terms.{{ $loop->index }}.parent_id">
                                                <flux:select.option value="">{{ __('Select Parent') }}</flux:select.option>
                                                @foreach ($this->form->terms as $parentTerm)
                                                    @if ($parentTerm['id'] != $term['id'])
                                                        <flux:select.option value="{{ $parentTerm['id'] }}">{{ $parentTerm['name'] }}</flux:select.option>
                                                    @endif
                                                @endforeach
                                            </flux:select>
                                        </flux:table.cell>
                                        <flux:table.cell class="flex justify-end">
                                            <flux:button icon="trash" variant="danger" size="sm" wire:click="deleteTerm({{ $term['id'] }})" />
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                @endif
            </flux:card>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <flux:button wire:navigate href="{{ route('taxonomies.index') }}" type="button" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Update Taxonomy') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
</div>
