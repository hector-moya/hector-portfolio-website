<div>
    <div class="mx-auto w-full max-w-4xl">
        <div class="flex flex-col gap-6">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">{{ __('New Navigation') }}</flux:heading>
                <flux:text>{{ __('Create a new navigation menu') }}</flux:text>
            </div>

            <form wire:submit="save" class="flex flex-col gap-6">
                {{-- Basic Info Card --}}
                <flux:card>
                    <flux:heading size="lg" class="mb-4">{{ __('Basic Information') }}</flux:heading>

                    <div class="flex flex-col gap-6">
                        {{-- Name --}}
                        <flux:input label="{{ __('Name') }}" placeholder="{{ __('Main Menu') }}" badge="{{ __('Required') }}" wire:model.live.debounce.750ms="form.name" />

                        {{-- Handle --}}
                        <flux:input label="{{ __('Handle') }}" placeholder="main-menu" badge="{{ __('Required') }}" wire:model="form.handle" />

                        {{-- Description --}}
                        <flux:input label="{{ __('Description') }}" placeholder="{{ __('Primary navigation menu for the website header...') }}" badge="{{ __('Optional') }}" rows="3" wire:model="form.description" />

                        {{-- Status --}}
                        <div class="flex justify-end">
                            <flux:switch label="{{ $form->is_active ? __('Active') : __('Draft') }}" wire:model.live="form.is_active" />
                        </div>
                    </div>
                </flux:card>

                {{-- Actions --}}
                <div class="flex items-center justify-between">
                    <flux:button variant="outline" :href="route('navigation.index')" wire:navigate>
                        {{ __('Cancel') }}
                    </flux:button>

                    <flux:button type="submit">
                        {{ __('Create Navigation') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
