<div>
    <flux:tab.group>
        <flux:tabs variant="segmented">
            @foreach ($tabs as $id => $tab)
                <flux:tab :name="$id" :key="'tab-'.$id">
                    {{ $tab }}
                </flux:tab>
            @endforeach
            <flux:modal.trigger name="add-tab-modal">
                <flux:tooltip content="{{ __('Add Tab') }}" class="flex items-center">
                    <flux:tab icon="plus" action></flux:tab>
                </flux:tooltip>
            </flux:modal.trigger>
        </flux:tabs>
        @foreach ($tabs as $id => $tab)
            <flux:tab.panel :name="$id" class="space-y-6" :key="'panel-'.$id">
                @foreach ($this->sections as $section)
                    <livewire:blueprints.components.section-card :key="'section-' . $section['id']" :$section />
                @endforeach
                <flux:card class="max-w-1/2 mx-auto flex justify-center border-2 border-dashed px-16">
                    <flux:button icon="plus" variant="ghost" wire:click="addSection" tooltip="{{ __('Click to add a new section.') }}">{{ __('Add Section') }}</flux:button>
                </flux:card>
            </flux:tab.panel>
        @endforeach
    </flux:tab.group>
    {{-- Add Tab Modal --}}
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

    {{-- Select Field Type Modal --}}
    {{-- <flux:modal name="select-field-modal">
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
    </flux:modal> --}}

    {{-- Edit Section Modal --}}
    <flux:modal name="edit-section-modal-{{ $section['id'] ?? '' }}">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Edit Section') }}</flux:heading>
            <flux:input label="{{ __('Section Name') }}" placeholder="{{ __('Main Content') }}" wire:model="sections.{{ $section['id'] ?? '' }}.name" />
            <flux:textarea label="{{ __('Section Instructions') }}" placeholder="{{ __('Instructions for this section...') }}" rows="3" wire:model="sections.{{ $section['id'] ?? '' }}.instructions" />
            <div class="flex justify-end">
                <flux:button type="button" @click="$flux.modal('edit-section-modal-{{ $section['id'] ?? '' }}').close()" variant="outline" class="mr-2">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="button" wire:click="updateSection">
                    {{ __('Update Section') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Edit Tab Modal --}}
    <flux:modal name="edit-tab-modal-{{ $id ?? '' }}">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Edit Tab') }}</flux:heading>
            <flux:input label="{{ __('Tab Name') }}" placeholder="{{ __('Content') }}" wire:model="editingTab.name" />
            <div class="flex justify-end">
                <flux:button type="button" @click="$flux.modal('edit-tab-modal-{{ $id ?? '' }}').close()" variant="outline" class="mr-2">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="button" wire:click="updateTab">
                    {{ __('Update Tab') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
