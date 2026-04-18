<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">{{ __('Create Collection') }}</flux:heading>
                <flux:text>{{ __('Define a new content collection') }}</flux:text>
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

                {{-- Theme --}}
                <flux:select label="{{ __('Theme') }}" wire:model="form.theme" description="{{ __('The visual theme used to render this collection\'s pages') }}">
                    <flux:select.option value="">{{ __('Default (Greenpeace)') }}</flux:select.option>
                    <flux:select.option value="greenpeace">{{ __('Greenpeace') }}</flux:select.option>
                </flux:select>

                {{-- Collection Type --}}
                <flux:select
                    label="{{ __('Collection Type') }}"
                    wire:model.live="form.type"
                    description="{{ __('Main: CMS-editable landing page with child collections. Child: entries accessible at /{parent}/{slug}. Standard: entry listing. Single Page: one entry (landing pages).') }}"
                >
                    <flux:select.option value="standard">{{ __('Standard') }}</flux:select.option>
                    <flux:select.option value="single">{{ __('Single Page') }}</flux:select.option>
                    <flux:select.option value="main">{{ __('Main (parent with child collections)') }}</flux:select.option>
                    <flux:select.option value="child">{{ __('Child (entries under a parent collection)') }}</flux:select.option>
                </flux:select>

                {{-- Parent Collection (child type only) --}}
                @if($form->type === 'child')
                <flux:select label="{{ __('Parent Collection') }}" wire:model="form.parent_id" description="{{ __('Entries will be accessible at /{parent-slug}/{entry-slug}') }}" badge="{{ __('Required') }}">
                    <flux:select.option value="">{{ __('Select a parent collection') }}</flux:select.option>
                    @foreach ($mainCollections as $mainCollection)
                        <flux:select.option value="{{ $mainCollection->id }}">{{ $mainCollection->name }}</flux:select.option>
                    @endforeach
                </flux:select>
                @endif

                {{-- Index Template (standard and main types) --}}
                @if(in_array($form->type, ['standard', 'main']))
                <flux:select label="{{ __('Index Template') }}" wire:model="form.index_template" description="{{ __('Layout used for the collection listing page') }}">
                    <flux:select.option value="">{{ __('Default (Card Grid)') }}</flux:select.option>
                    @foreach (\App\Support\TemplateLayouts::indexTemplates() as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
                @endif


                {{-- Status --}}
                <div class="flex justify-end">
                    <flux:switch label="{{ $form->is_active ? __('Active') : __('Inactive') }}" wire:model.live="form.is_active" />
                </div>
            </flux:card>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <flux:button wire:navigate href="{{ route('collections.index') }}" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Create Collection') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
