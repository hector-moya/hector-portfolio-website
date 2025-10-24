<div>
    <div class="mx-auto w-full max-w-4xl">
        <div class="flex flex-col gap-6">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">{{ __('Edit Taxonomy') }}</flux:heading>
                <flux:text>{{ __('Update your content organization structure') }}</flux:text>
            </div>

            <form wire:submit="save" class="flex flex-col gap-6">
                {{-- Basic Info Card --}}
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('Basic Information') }}</flux:heading>

                    <div class="flex flex-col gap-6">
                        {{-- Name --}}
                        <flux:input label="{{ __('Name') }}" placeholder="{{ __('Categories') }}" badge="{{ __('Required') }}" wire:model.live.debounce.750ms="form.name" />

                        {{-- Slug --}}
                        <flux:input label="{{ __('Slug') }}" placeholder="categories" badge="{{ __('Required') }}" wire:model="form.slug" />

                        {{-- Description --}}
                        <flux:textarea label="{{ __('Description') }}" placeholder="Main categories for the blog..." badge="{{ __('Optional') }}" rows="3" wire:model="form.description" />

                        {{-- Status --}}
                        <div class="flex justify-end">
                            <flux:switch label="{{ $form->is_active ? 'Active' : 'Draft' }}" wire:model.live="form.is_active" />
                        </div>
                    </div>
                </flux:card>

                {{-- Terms Card --}}
                <flux:card>
                    <div class="mb-4 flex items-center justify-between">
                        <flux:heading size="lg">{{ __('Terms') }}</flux:heading>
                        <flux:button size="sm" wire:click="$dispatch('openModal', 'add-term')" icon="plus" variant="primary">
                            {{ __('Add Term') }}
                        </flux:button>
                    </div>

                    @if ($this->terms->isEmpty())
                        <div class="rounded-lg border-2 border-dashed border-zinc-300 p-8 text-center dark:border-zinc-600">
                            <flux:text>{{ __('No terms added yet. Click "Add Term" to get started.') }}</flux:text>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($this->terms as $term)
                                <flux:card wire:key="term-{{ $term->id }}">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <flux:heading level="5">{{ $term->name }}</flux:heading>
                                            @if ($term->description)
                                                <flux:text>{{ $term->description }}</flux:text>
                                            @endif
                                        </div>
                                        <flux:dropdown>
                                            <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                            <flux:menu>
                                                <flux:menu.item icon="pencil" wire:click="editTerm({{ $term->id }})">
                                                    {{ __('Edit') }}
                                                </flux:menu.item>
                                                <flux:menu.separator />
                                                <flux:menu.item variant="danger" icon="trash" wire:click="deleteTerm({{ $term->id }})" wire:confirm="Are you sure you want to delete this term?">
                                                    {{ __('Delete') }}
                                                </flux:menu.item>
                                            </flux:menu>
                                        </flux:dropdown>
                                    </div>
                                </flux:card>
                            @endforeach
                        </div>
                    @endif
                </flux:card>

                {{-- Actions --}}
                <div class="flex items-center justify-between gap-3">
                    <flux:button wire:click="delete" type="button" variant="danger" wire:confirm="Are you sure you want to delete this taxonomy?">
                        {{ __('Delete Taxonomy') }}
                    </flux:button>

                    <div class="flex items-center gap-3">
                        <flux:button wire:navigate href="{{ route('taxonomies.index') }}" type="button" variant="ghost">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            {{ __('Update Taxonomy') }}
                        </flux:button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
