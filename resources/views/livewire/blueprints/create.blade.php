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
                <flux:card class="space-y-4 !px-0">
                    {{-- Name --}}
                    <div class="px-6">
                        <flux:input label="{{ __('Name') }}" placeholder="{{ __('Blog Post') }}" badge="{{ __('Required') }}" wire:model.live.debounce.750ms="form.name" />
                    </div>
                    <flux:separator />

                    {{-- Slug --}}
                    <div class="px-6">
                        <flux:input label="{{ __('Slug') }}" placeholder="{{ __('blog-post') }}" badge="{{ __('Required') }}" wire:model="form.slug" />
                    </div>
                    <flux:separator />

                    {{-- Description --}}
                    <div class="px-6">
                        <flux:textarea label="{{ __('Description') }}" placeholder="Structure for blog posts..." rows="3" badge="{{ __('Optional') }}" wire:model="form.description" class="py-4" />
                    </div>
                    <flux:separator />

                    {{-- Status --}}
                    <div class="flex justify-end px-6">
                        <flux:switch label="{{ $form->is_active ? 'Active' : 'Draft' }}" wire:model.live="form.is_active" />
                    </div>
                </flux:card>

                {{-- Fields Card --}}
                <flux:heading size="lg">{{ __('Fields') }}</flux:heading>

                <livewire:blueprints.components.blueprint-tabs :form="$form" />

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <flux:button wire:navigate href="{{ route('blueprints.index') }}" type="button" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Save') }}
                    </flux:button>
                </div>
            </form>

        </div>
    </div>
</div>
