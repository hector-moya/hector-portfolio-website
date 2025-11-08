<div>
    <flux:tab.group>
        <flux:tabs variant="segmented">
            @foreach ($tabs as $tabIndex => $tab)
                <flux:tab :name="$tabIndex" :key="'tab-'.$tabIndex">
                    {{ $tab->name }}
                    <flux:icon.chevron-down variant="micro" wire:click="openEditModal({{ $tab->id }})" class="hover: cursor-pointer transition-transform duration-200 hover:scale-110 hover:opacity-30" />
                </flux:tab>
            @endforeach
            <flux:modal.trigger name="add-tab-modal">
                <flux:tooltip content="{{ __('Add Tab') }}" class="flex items-center">
                    <flux:tab icon="plus" action></flux:tab>
                </flux:tooltip>
            </flux:modal.trigger>
        </flux:tabs>
        @foreach ($tabs as $tabIndex => $tab)
            <flux:tab.panel :name="$tabIndex" class="space-y-6" :key="'panel-'.$tabIndex">
                @foreach ($tab->sections ?? [] as $sectionIndex => $section)
                    <livewire:blueprints.components.section-card :sectionId="$section->id" :key="$section->id" :tabId="$tab->id" />
                @endforeach
                <flux:card class="max-w-1/2 mx-auto flex justify-center border-2 border-dashed px-16">
                    <flux:button icon="plus" variant="ghost" wire:click="addSection({{ $tab->id }})" tooltip="{{ __('Click to add a new section.') }}">{{ __('Add Section') }}</flux:button>
                </flux:card>
            </flux:tab.panel>

            {{-- Edit Tab Modal --}}
            <flux:modal name="edit-tab-modal-{{ $tab->id }}" :closable="false" :key="'panel-'.$tabIndex">
                <div class="space-y-6">
                    <div class="flex justify-between">
                        <flux:heading size="lg">{{ __('Edit Tab') }}</flux:heading>
                        <flux:button icon="trash" variant="danger" size="xs" wire:click="deleteTab({{ $tab->id ?? '' }})" tooltip="{{ __('Delete Tab') }}" />
                    </div>
                    <flux:input label="{{ __('Tab Name') }}" placeholder="{{ __('Content') }}" wire:model.live.debounce.750ms="form.name" />
                    <flux:input label="{{ __('Tab Handle') }}" placeholder="{{ __('content') }}" wire:model="form.handle" />
                    <div class="flex justify-end">
                        <flux:button type="button" @click="$flux.modal('edit-tab-modal-{{ $tab->id ?? '' }}').close()" variant="outline" class="mr-2">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button type="button" wire:click="updateTab({{ $tab->id ?? '' }})">
                            {{ __('Update Tab') }}
                        </flux:button>
                    </div>
                </div>
            </flux:modal>
        @endforeach
    </flux:tab.group>
    {{-- Add Tab Modal --}}
    <flux:modal name="add-tab-modal">
        <div class="space-y-6">
            <flux:heading size="lg">{{ __('Add New Tab') }}</flux:heading>
            <flux:input label="{{ __('Tab Name') }}" placeholder="{{ __('Content') }}" wire:model.live.debounce.750ms="form.name" />
            <flux:input label="{{ __('Tab Handle') }}" placeholder="{{ __('content') }}" wire:model="form.handle" />
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

</div>
