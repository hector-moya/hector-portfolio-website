<div>
    <div class="mx-auto w-full max-w-4xl">
        <div class="flex flex-col gap-6">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">{{ __('Edit Navigation') }}</flux:heading>
                <flux:text>{{ __('Update your navigation menu') }}</flux:text>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                {{-- Settings --}}
                <div class="lg:col-span-1">
                    <form wire:submit="save" class="flex flex-col gap-6">
                        <flux:card>
                            <flux:heading size="lg" class="mb-4">{{ __('Settings') }}</flux:heading>

                            <div class="flex flex-col gap-6">
                                {{-- Name --}}
                                <flux:input label="{{ __('Name') }}" placeholder="{{ __('Main Menu') }}" badge="{{ __('Required') }}" wire:model.live.debounce.750ms="form.name" />

                                {{-- Handle --}}
                                <flux:input label="{{ __('Handle') }}" placeholder="main-menu" badge="{{ __('Required') }}" wire:model="form.handle" />

                                {{-- Description --}}
                                <flux:input label="{{ __('Description') }}" placeholder="{{ __('Primary navigation menu...') }}" badge="{{ __('Optional') }}" rows="3" wire:model="form.description" />

                                {{-- Status --}}
                                <div class="flex justify-end">
                                    <flux:switch label="{{ $form->is_active ? __('Active') : __('Draft') }}" wire:model.live="form.is_active" />
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-6 flex items-center justify-between">
                                <flux:button variant="outline" :href="route('navigation.index')" wire:navigate>
                                    {{ __('Back') }}
                                </flux:button>

                                <flux:button type="submit">
                                    {{ __('Save Changes') }}
                                </flux:button>
                            </div>
                        </flux:card>
                    </form>
                </div>

                {{-- Items --}}
                <div class="lg:col-span-2">
                    <flux:card>
                        <flux:heading size="lg" class="mb-4">{{ __('Menu Items') }}</flux:heading>

                        {{-- Add Item Form --}}
                        <form wire:submit="addItem" class="mb-6 flex flex-col gap-4">
                            <div class="flex gap-4">
                                <flux:input label="{{ __('Title') }}" placeholder="{{ __('Home') }}" class="flex-1" wire:model="newItemTitle" />
                                <flux:input label="{{ __('URL') }}" placeholder="{{ __('/') }}" class="flex-1" wire:model="newItemUrl" />
                            </div>

                            <div class="flex justify-end">
                                <flux:button type="submit">
                                    {{ __('Add Item') }}
                                </flux:button>
                            </div>
                        </form>

                        {{-- Item List --}}
                        @if($items->isEmpty())
                            <flux:card class="rounded-lg border-2 border-dashed border-neutral-300 p-8 text-center dark:border-neutral-600">
                                <flux:text>{{ __('No items added yet.') }}</flux:text>
                            </flux:card>
                        @else
                            <div wire:sortable="reorder" class="flex flex-col gap-2">
                                @foreach($items as $item)
                                    <div wire:key="item-{{ $item->id }}" wire:sortable.item="{{ $item->id }}" class="relative flex items-center gap-4 rounded-lg border border-neutral-200 bg-neutral-50 p-4 dark:border-neutral-700 dark:bg-neutral-800">
                                        <div wire:sortable.handle class="cursor-move">
                                            <flux:icon.chevron-up-down class="h-5 w-5 text-neutral-400" />
                                        </div>

                                        @if($editingItem?->is($item))
                                            <div class="flex flex-1 gap-4">
                                                <flux:input placeholder="{{ __('Title') }}" class="flex-1" wire:model="editingItem.title" />
                                                <flux:input placeholder="{{ __('URL') }}" class="flex-1" wire:model="editingItem.url" />

                                                <div class="flex items-center gap-2">
                                                    <flux:button size="sm" wire:click="updateItem">{{ __('Save') }}</flux:button>
                                                    <flux:button size="sm" variant="outline" wire:click="$set('editingItem', null)">{{ __('Cancel') }}</flux:button>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex-1">
                                                <div class="font-medium">{{ $item->title }}</div>
                                                <div class="text-sm text-neutral-500 dark:text-neutral-400">{{ $item->url }}</div>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <flux:button size="sm" variant="ghost" wire:click="editItem({{ $item->id }})">
                                                    <flux:icon name="pencil" class="h-4 w-4" />
                                                </flux:button>

                                                <flux:button size="sm" variant="ghost" wire:click="deleteItem({{ $item->id }})" wire:confirm="{{ __('Are you sure you want to delete this item?') }}">
                                                    <flux:icon name="trash" class="h-4 w-4" />
                                                </flux:button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </flux:card>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        Alpine.data('navigationBuilder', () => ({
            init() {
                new Sortable(this.$el.querySelector('[wire\\:sortable]'), {
                    animation: 150,
                    handle: '[wire\\:sortable\\.handle]',
                    ghostClass: 'opacity-50',
                    onEnd: (evt) => {
                        this.$wire.reorder(this.getOrder());
                    }
                });
            },
            getOrder() {
                return Array.from(this.$el.querySelectorAll('[wire\\:sortable\\.item]')).map((el) => {
                    return {
                        id: el.getAttribute('wire:sortable.item'),
                        children: []
                    };
                });
            }
        }));
    </script>
    @endscript
</div>
