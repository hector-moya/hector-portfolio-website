<div>
    <x-themes.wrapper :theme="$theme">
        @if ($entry)
            <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="mb-12 space-y-8 text-center">
                    <flux:heading class="!text-4xl">
                        {{ $entry->elements->firstWhere('handle', 'heading')?->value ?? 'Contact Us' }}
                    </flux:heading>
                    <flux:text class="mx-auto max-w-2xl !text-lg">
                        {!! nl2br(e($entry->elements->firstWhere('handle', 'description')?->value)) !!}
                    </flux:text>
                </div>

                <div class="flex justify-center">
                    <flux:card class="w-full space-y-6 p-8 shadow-xl">
                        <flux:heading class="!text-2xl font-bold">{{ __('Send a Message') }}</flux:heading>

                        @if ($submitted)
                            <div class="rounded-lg bg-green-50 p-6 text-center dark:bg-green-950/20">
                                <flux:icon.check-circle class="mx-auto mb-3 size-10 text-green-500" />
                                <flux:heading size="lg">{{ __('Message Sent!') }}</flux:heading>
                                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
                                    {{ __("Thank you for reaching out. We'll get back to you soon.") }}
                                </flux:text>
                                <flux:button class="mt-4" variant="ghost" wire:click="$set('submitted', false)">
                                    {{ __('Send another message') }}
                                </flux:button>
                            </div>
                        @else
                            <form wire:submit="submit" class="space-y-4">
                                <div>
                                    <flux:input wire:model="form.name" name="name" label="{{ __('Name') }}" type="text" />
                                    @error('name') <flux:error>{{ $message }}</flux:error> @enderror
                                </div>
                                <div>
                                    <flux:input wire:model="form.email" name="email" label="{{ __('Email') }}" type="email" />
                                    @error('email') <flux:error>{{ $message }}</flux:error> @enderror
                                </div>
                                <div>
                                    <flux:textarea wire:model="form.message" name="message" label="{{ __('Message') }}" rows="4" />
                                    @error('message') <flux:error>{{ $message }}</flux:error> @enderror
                                </div>
                                <div class="flex justify-end">
                                    <flux:button type="submit" variant="primary" icon-trailing="paper-airplane" wire:loading.attr="disabled">
                                        <span wire:loading.remove>{{ __('Send Message') }}</span>
                                        <span wire:loading>{{ __('Sending...') }}</span>
                                    </flux:button>
                                </div>
                            </form>
                        @endif
                    </flux:card>
                </div>
            </div>
        @else
            <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
                <div class="flex justify-center">
                    <flux:card class="w-full space-y-6 p-8 shadow-xl">
                        <flux:heading class="!text-2xl font-bold">{{ __('Contact Us') }}</flux:heading>

                        @if ($submitted)
                            <div class="rounded-lg bg-green-50 p-6 text-center dark:bg-green-950/20">
                                <flux:icon.check-circle class="mx-auto mb-3 size-10 text-green-500" />
                                <flux:heading size="lg">{{ __('Message Sent!') }}</flux:heading>
                                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
                                    {{ __("Thank you for reaching out. We'll get back to you soon.") }}
                                </flux:text>
                                <flux:button class="mt-4" variant="ghost" wire:click="$set('submitted', false)">
                                    {{ __('Send another message') }}
                                </flux:button>
                            </div>
                        @else
                            <form wire:submit="submit" class="space-y-4">
                                <div>
                                    <flux:input wire:model="form.name" name="name" label="{{ __('Name') }}" type="text" />
                                    @error('name') <flux:error>{{ $message }}</flux:error> @enderror
                                </div>
                                <div>
                                    <flux:input wire:model="form.email" name="email" label="{{ __('Email') }}" type="email" />
                                    @error('email') <flux:error>{{ $message }}</flux:error> @enderror
                                </div>
                                <div>
                                    <flux:textarea wire:model="form.message" name="message" label="{{ __('Message') }}" rows="4" />
                                    @error('message') <flux:error>{{ $message }}</flux:error> @enderror
                                </div>
                                <div class="flex justify-end">
                                    <flux:button type="submit" variant="primary" icon-trailing="paper-airplane" wire:loading.attr="disabled">
                                        <span wire:loading.remove>{{ __('Send Message') }}</span>
                                        <span wire:loading>{{ __('Sending...') }}</span>
                                    </flux:button>
                                </div>
                            </form>
                        @endif
                    </flux:card>
                </div>
            </div>
        @endif
    </x-themes.wrapper>
</div>
