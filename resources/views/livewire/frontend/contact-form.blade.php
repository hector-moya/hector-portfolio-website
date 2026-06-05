<div>
    <section class="bg-zinc-50 py-16 dark:bg-zinc-800/50 sm:py-24">
        <div class="mx-auto max-w-2xl px-6 lg:px-8">
            @if ($title)
                <div class="mb-10 text-center">
                    <flux:heading class="text-3xl! font-bold sm:text-4xl!" level="2">
                        {{ $title }}
                    </flux:heading>
                </div>
            @endif

            <div class="rounded-2xl border border-zinc-200 bg-white p-8 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                @if ($sent)
                    <flux:callout variant="success" icon="check-circle">
                        <flux:heading>{{ __('Message sent') }}</flux:heading>
                        <flux:text>{{ __('Thanks for reaching out — we\'ll get back to you soon.') }}</flux:text>
                    </flux:callout>
                @else
                    <form wire:submit="submit" class="space-y-5">
                        <flux:input
                            wire:model="form.name"
                            :label="__('Name')"
                            type="text"
                            required
                            autocomplete="name"
                        />

                        <flux:input
                            wire:model="form.email"
                            :label="__('Email')"
                            type="email"
                            required
                            autocomplete="email"
                            placeholder="email@example.com"
                        />

                        <flux:textarea
                            wire:model="form.message"
                            :label="__('Message')"
                            rows="4"
                            required
                        />

                        {{-- Honeypot: visually hidden and ignored by humans, skipped by assistive tech --}}
                        <div aria-hidden="true" class="hidden">
                            <label>
                                {{ __('Leave this field empty') }}
                                <input type="text" wire:model="website" tabindex="-1" autocomplete="off" />
                            </label>
                        </div>

                        <div class="pt-2">
                            <flux:button variant="primary" type="submit" class="w-full">
                                <span wire:loading.remove wire:target="submit">{{ __('Submit') }}</span>
                                <span wire:loading wire:target="submit">{{ __('Sending...') }}</span>
                            </flux:button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </section>
</div>
