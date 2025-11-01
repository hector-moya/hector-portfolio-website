<div>
    <div class="space-y-4">
        {{-- Tabs List --}}
        <div>
            <flux:heading size="lg">{{ __('Tabs') }}</flux:heading>

            <div class="space-y-4 mt-4">
                @foreach($tabs as $tab)
                    <flux:card>
                        <div class="p-4 space-y-4">
                            <div class="flex items-center justify-between">
                                <flux:input
                                    wire:model="tabs.{{ $tab->id }}.name"
                                    placeholder="{{ __('Tab Name') }}"
                                />
                                <flux:button
                                    variant="ghost"
                                    wire:click="deleteTab('{{ $tab->id }}')"
                                >
                                    <flux:icon name="trash" class="w-4 h-4" />
                                </flux:button>
                            </div>

                            {{-- Sections within this tab --}}
                            <div class="pl-4 space-y-4">
                                @foreach($tab->sections as $section)
                                    <div class="border-l-2 border-gray-200 pl-4">
                                        @include('livewire.blueprints.partials.section', ['section' => $section])
                                    </div>
                                @endforeach

                                <flux:button
                                    variant="ghost"
                                    wire:click="addSection('{{ $tab->id }}')"
                                    class="ml-4"
                                >
                                    <flux:icon name="plus" class="w-4 h-4 mr-2" />
                                    {{ __('Add Section') }}
                                </flux:button>
                            </div>
                        </div>
                    </flux:card>
                @endforeach

                <flux:button
                    variant="ghost"
                    wire:click="addTab"
                >
                    <flux:icon name="plus" class="w-4 h-4 mr-2" />
                    {{ __('Add Tab') }}
                </flux:button>
            </div>
        </div>

        {{-- Root Sections (not in tabs) --}}
        <div>
            <flux:heading size="lg">{{ __('Sections') }}</flux:heading>

            <div class="space-y-4 mt-4">
                @foreach($rootSections as $section)
                    <flux:card>
                        <div class="p-4">
                            @include('livewire.blueprints.partials.section', ['section' => $section])
                        </div>
                    </flux:card>
                @endforeach

                <flux:button
                    variant="ghost"
                    wire:click="addSection"
                >
                    <flux:icon name="plus" class="w-4 h-4 mr-2" />
                    {{ __('Add Section') }}
                </flux:button>
            </div>
        </div>
    </div>
</div>
