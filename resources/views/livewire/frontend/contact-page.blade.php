<div>
    @if($entry)
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-zinc-900 dark:text-white">
                    {{ $entry->elements->firstWhere('handle', 'heading')?->value ?? 'Contact Us' }}
                </h1>
                @if($description = $entry->elements->firstWhere('handle', 'description')?->value)
                    <div class="mt-4 text-lg text-zinc-600 dark:text-zinc-400 max-w-2xl mx-auto">
                        {!! nl2br(e($description)) !!}
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Contact Information --}}
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mb-6">Get in Touch</h2>

                    <div class="space-y-4">
                        @if($email = $entry->elements->firstWhere('handle', 'email')?->value)
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Email</h3>
                                <a href="mailto:{{ $email }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $email }}
                                </a>
                            </div>
                        @endif

                        @if($phone = $entry->elements->firstWhere('handle', 'phone')?->value)
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Phone</h3>
                                <a href="tel:{{ $phone }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $phone }}
                                </a>
                            </div>
                        @endif

                        @if($address = $entry->elements->firstWhere('handle', 'address')?->value)
                            <div>
                                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Address</h3>
                                <address class="not-italic text-zinc-600 dark:text-zinc-400">
                                    {!! nl2br(e($address)) !!}
                                </address>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Contact Form Placeholder --}}
                <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mb-6">Send a Message</h2>
                    <form class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Name</label>
                            <input type="text" class="w-full px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 dark:bg-zinc-900 focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Email</label>
                            <input type="email" class="w-full px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 dark:bg-zinc-900 focus:ring-2 focus:ring-indigo-600 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Message</label>
                            <textarea rows="4" class="w-full px-4 py-2 rounded-lg border border-zinc-300 dark:border-zinc-600 dark:bg-zinc-900 focus:ring-2 focus:ring-indigo-600 focus:border-transparent"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
            <h1 class="text-4xl font-bold text-zinc-900 dark:text-white">Contact</h1>
            <p class="mt-4 text-zinc-600 dark:text-zinc-400">Content coming soon...</p>
        </div>
    @endif
</div>
