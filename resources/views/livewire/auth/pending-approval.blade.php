<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Account Pending Approval')"
        :description="__('Your account is under review. You\'ll receive an email once an administrator approves your registration.')"
    />

    <flux:card class="text-center">
        <flux:icon.clock class="mx-auto mb-4 size-12 text-yellow-500" />
        <flux:heading>{{ __('Hang tight!') }}</flux:heading>
        <flux:subheading class="mt-2">
            {{ __('Our team will review your account and notify you by email when you\'re approved.') }}
        </flux:subheading>
    </flux:card>

    <div class="text-center text-sm">
        <flux:link :href="route('logout')" wire:navigate>{{ __('Back to login') }}</flux:link>
    </div>
</div>
