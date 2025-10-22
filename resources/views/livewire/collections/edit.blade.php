<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">{{ __('Edit Collection') }}</flux:heading>
                <flux:text>{{ __('Update collection settings') }}</flux:text>
            </div>
            <flux:button icon="arrow-uturn-left" wire:navigate href="{{ route('collections.index') }}">
                {{ __('Return') }}
            </flux:button>
        </div>

        {{-- Form Card --}}
        <form wire:submit="save" class="flex flex-col gap-6">
            <flux:card class="space-y-6">
                {{-- Name --}}
                <flux:input label="{{ __('Name') }}" wire:model.live="form.name" placeholder="{{ __('Blog Posts') }}" badge="{{ __('Required') }}" description="{{ __('A descriptive name for your collection') }}" />

                {{-- Slug --}}
                <flux:input label="{{ __('Slug') }}" wire:model="form.slug" placeholder="{{ __('blog-posts') }}" description="{{ __('URL-friendly identifier (auto-generated if left blank)') }}" />

                {{-- Description --}}
                <flux:textarea label="{{ __('Description') }}" wire:model="form.description" placeholder="{{ __('A collection of blog articles...') }}" rows="3" />

                {{-- Blueprint --}}
                <flux:select label="{{ __('Blueprint') }}" wire:model="form.blueprint_id" description="{{ __('The structure that entries in this collection will follow') }}">
                    <flux:select.option value="">{{ __('Select a blueprint') }}</flux:select.option>
                    @foreach ($blueprints as $blueprint)
                        <flux:select.option value="{{ $blueprint->id }}">{{ $blueprint->name }}</flux:select.option>
                    @endforeach
                </flux:select>

                {{-- Status --}}
                <div class="flex justify-end">
                    <flux:switch label="{{ $form->is_active ? __('Active') : __('Inactive') }}" wire:model="form.is_active" />
                </div>
            </flux:card>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <flux:button wire:navigate href="{{ route('collections.index') }}" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Update Collection') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
