<div>
    <x-themes.greenpeace>
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

                    {{-- Contact Form Placeholder --}}
                    <flux:card class="w-full space-y-6 p-8 shadow-xl">
                        <flux:heading class="!text-2xl font-bold">{{ __('Send a Message') }}</flux:heading>
                        <form class="space-y-4">
                            <div>
                                <flux:input name="name" label="Name" type="text" />
                            </div>
                            <div>
                                <flux:input name="email" label="Email" type="email" />
                            </div>
                            <div>
                                <flux:textarea name="message" label="Message" rows="4" />
                            </div>
                            <div class="flex justify-end">
                                <flux:button type="submit" variant="primary" icon-trailing="paper-airplane">
                                    {{ __('Send Message') }}
                                </flux:button>
                            </div>
                        </form>
                    </flux:card>
                </div>
            </div>
        @else
            <div class="mx-auto max-w-7xl space-y-8 px-4 py-24 text-center sm:px-6 lg:px-8">
                <flux:heading class="!text-4xl">Contact</flux:heading>
                <flux:text>Content coming soon...</flux:text>
            </div>
        @endif
    </x-themes.greenpeace>
</div>
