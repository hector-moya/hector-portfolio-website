<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Security')" :subheading="__('Control who can register for an account')">
        <form wire:submit="saveMode" class="my-6 w-full space-y-6">
            <div class="space-y-4">
                <flux:heading size="sm">{{ __('Registration Mode') }}</flux:heading>
                <flux:radio.group wire:model="registrationMode" class="space-y-3">
                    <flux:radio
                        value="closed"
                        :label="__('Closed')"
                        :description="__('Registration is disabled. No one can create an account.')"
                    />
                    <flux:radio
                        value="invitation"
                        :label="__('Invitation Only')"
                        :description="__('Only users you invite via email can register.')"
                    />
                    <flux:radio
                        value="approval"
                        :label="__('Approval Required')"
                        :description="__('Anyone can register, but you must manually approve each account.')"
                    />
                    <flux:radio
                        value="open"
                        :label="__('Open')"
                        :description="__('Anyone can register immediately. Use with caution.')"
                    />
                </flux:radio.group>
            </div>

            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
        </form>
    </x-settings.layout>
</section>
