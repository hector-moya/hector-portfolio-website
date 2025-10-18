<div>
    @if ($entry)
        <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <h1 class="text-4xl font-bold text-zinc-900 dark:text-white">
                    {{ $entry->elements->firstWhere('handle', 'heading')?->value ?? 'Contact Us' }}
                </h1>
                @if ($description = $entry->elements->firstWhere('handle', 'description')?->value)
                    <div class="mx-auto mt-4 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
                        {!! nl2br(e($description)) !!}
                    </div>
                @endif
            </div>

            <div class="flex justify-center">

                {{-- Contact Form Placeholder --}}
                <flux:card class="w-full space-y-6 p-8 shadow-xl">
                    <flux:heading class="!text-2xl font-bold">Send a Message</flux:heading>
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
        <div class="mx-auto max-w-7xl px-4 py-24 text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-zinc-900 dark:text-white">Contact</h1>
            <p class="mt-4 text-zinc-600 dark:text-zinc-400">Content coming soon...</p>
        </div>
    @endif
</div>
