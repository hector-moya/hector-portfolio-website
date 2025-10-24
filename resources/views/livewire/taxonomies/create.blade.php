<div>
    <div class="mx-auto w-full max-w-4xl">
        <div class="flex flex-col gap-6">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">{{ __('Create Taxonomy') }}</flux:heading>
                <flux:text>{{ __('Define categories and tags to organize your content') }}</flux:text>
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

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3">
                    <flux:button wire:navigate href="{{ route('taxonomies.index') }}" type="button" variant="ghost">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ __('Create Taxonomy') }}
                    </flux:button>
                </div>
            </form>
        </div>
    </div>
</div>
