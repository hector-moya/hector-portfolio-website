<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">{{ __('Edit User') }}</flux:heading>
                <flux:text>{{ __('Update user account details') }}</flux:text>
            </div>
            <flux:button icon="arrow-uturn-left" wire:navigate href="{{ route('users.index') }}">
                {{ __('Return') }}
            </flux:button>
        </div>

        {{-- Form Card --}}
        <form wire:submit="save" class="flex flex-col gap-6">
            <flux:card class="space-y-6">
                {{-- Name --}}
                <flux:input label="{{ __('Name') }}" badge="{{ __('Required') }}" wire:model="form.name" />

                {{-- Email --}}
                <flux:input label="{{ __('Email') }}" badge="{{ __('Required') }}" type="email" wire:model="form.email" placeholder="john@example.com" />

                {{-- Password --}}
                <flux:input label="{{ __('Password') }}" type="password" wire:model="form.password" placeholder="••••••••" description="{{ __('Leave blank to keep current password') }}" />

                {{-- Password Confirmation --}}
                <flux:input label="{{ __('Confirm Password') }}" type="password" wire:model="form.password_confirmation" placeholder="••••••••" />

                {{-- Role --}}
                <flux:select label="{{ __('Role') }}" badge="{{ __('Required') }}" wire:model="form.role">
                    <flux:select.option value="viewer">{{ __('Viewer') }}</flux:select.option>
                    <flux:select.option value="editor">{{ __('Editor') }}</flux:select.option>
                    <flux:select.option value="admin">{{ __('Admin') }}</flux:select.option>
                </flux:select>

                <flux:callout icon="exclamation-triangle" variant="secondary" inline>
                    <div class="flex justify-between gap-6">
                        <div>
                            <flux:heading>{{ __('Viewer') }}</flux:heading>
                            <flux:text>{{ __('Can view content only') }}</flux:text>
                        </div>
                        <div>
                            <flux:heading>{{ __('Editor') }}</flux:heading>
                            <flux:text>{{ __('Can create and edit content') }}</flux:text>
                        </div>
                        <div>
                            <flux:heading>{{ __('Admin') }}</flux:heading>
                            <flux:text>{{ __('Full access to all features') }}</flux:text>
                        </div>
                    </div>
                </flux:callout>
            </flux:card>
            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <flux:button wire:navigate href="{{ route('users.index') }}" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary">
                    {{ __('Update User') }}
                </flux:button>
            </div>
        </form>
    </div>
</div>
