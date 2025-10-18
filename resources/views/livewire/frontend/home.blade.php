<div>
    @if ($entry)
        {{-- Hero Section --}}
        <div class="relative bg-gradient-to-r from-teal-900/80 to-green-900/80 text-white">
            <div class="relative isolate">
                <svg aria-hidden="true" class="h-256 mask-[radial-gradient(32rem_32rem_at_center,white,transparent)] absolute inset-x-0 top-0 -z-10 w-full stroke-white/10" viewBox="0 0 800 600" preserveAspectRatio="none">
                    <defs>
                        <!-- Basic grid pattern -->
                        <pattern id="simple-grid" width="100" height="100" x="50%" y="-1" patternUnits="userSpaceOnUse">
                            <!-- Vertical line -->
                            <line x1="0" y1="20" x2="0" y2="80" stroke="white" stroke-width="0.5" opacity="0.3" />
                            <!-- Horizontal line -->
                            <line x1="20" y1="0" x2="80" y2="0" stroke="white" stroke-width="0.5" opacity="0.3" />
                        </pattern>
                    </defs>

                    <!-- Render the grid -->
                    <rect width="100%" height="100%" fill="url(#simple-grid)" />

                    <!-- Triangle texture -->
                    <g x="50%" y="-1" fill="white" opacity="0.15">
                        <polygon points="0,200 100,0 200,200" />
                        <polygon points="200,200 300,0 400,200" />
                        <polygon points="100,400 0,200 200,200" />
                        <polygon points="300,400 200,200 400,200" />
                    </g>
                </svg>

                <!-- Organic background shape -->
                <div aria-hidden="true" class="absolute left-1/2 right-0 top-0 -z-10 -ml-24 transform-gpu overflow-hidden blur-3xl lg:ml-24 xl:ml-48">
                    <div style="clip-path: polygon(63.1% 29.5%, 100% 17.1%, 76.6% 3%, 48.4% 0%, 44.6% 4.7%, 54.5% 25.3%, 59.8% 49%, 55.2% 57.8%, 44.4% 57.2%, 27.8% 47.9%, 35.1% 81.5%, 0% 97.7%, 39.2% 100%, 35.2% 81.4%, 97.2% 52.8%, 63.1% 29.5%)"
                         class="aspect-[801/1036] w-[50rem] bg-gradient-to-tr from-teal-500 to-green-500 opacity-30"></div>
                </div>

                <div class="overflow-hidden">
                    <div class="mx-auto max-w-7xl px-6 pb-32 pt-36 sm:pt-60 lg:px-8 lg:pt-32">
                        <div class="mx-auto max-w-2xl gap-x-20 lg:mx-0 lg:flex lg:max-w-none lg:items-center">
                            <div class="relative w-full space-y-8 lg:shrink-0 xl:max-w-xl">
                                <flux:heading class="leading-16 animate-slide-in-from-top !text-6xl font-bold opacity-0" level="1">
                                    {{ $entry->elements->firstWhere('handle', 'hero_title')?->value ?? 'Welcome' }}
                                </flux:heading>
                                <flux:heading size="xl" class="animate-slide-in-from-left opacity-0">{{ $entry->elements->firstWhere('handle', 'hero_subtitle')?->value ?? 'Your subtitle here' }}</flux:heading>
                                <flux:text size="xl" class="animate-slide-in-from-left text-base leading-10 opacity-0">{{ $entry->elements->firstWhere('handle', 'content')?->value ?? 'Your content here' }}</flux:text>
                                <div class="animate-slide-in-from-right mt-10 flex items-center gap-x-6 opacity-0">
                                    <flux:button variant="primary" href="{{ $entry->elements->firstWhere('handle', 'cta_url_primary')?->value ?? '#' }}" class="shadow-xl">
                                        {{ $entry->elements->firstWhere('handle', 'cta_text_primary')?->value }}
                                    </flux:button>
                                    <flux:button href="{{ $entry->elements->firstWhere('handle', 'cta_url_secondary')?->value ?? '#' }}" class="shadow-xl">
                                        {{ $entry->elements->firstWhere('handle', 'cta_text_secondary')?->value }}
                                    </flux:button>
                                </div>
                            </div>
                            <div class="animate-slide-in-from-bottom mt-14 flex justify-end gap-8 opacity-0 sm:-mt-44 sm:justify-start sm:pl-20 lg:mt-0 lg:pl-0">
                                <div class="xl:order-0 ml-auto w-44 flex-none space-y-8 pt-32 sm:ml-0 sm:pt-80 lg:order-last lg:pt-36 xl:pt-80">
                                    <div class="relative">
                                        <img src="https://images.unsplash.com/photo-1557804506-669a67965ba0?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&h=528&q=80" alt=""
                                             class="aspect-2/3 w-full rounded-xl bg-gray-700/5 object-cover shadow-lg" />
                                        <div class="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-inset ring-white/10"></div>
                                    </div>
                                </div>
                                <div class="mr-auto w-44 flex-none space-y-8 sm:mr-0 sm:pt-52 lg:pt-36">
                                    <div class="relative">
                                        <img src="https://images.unsplash.com/photo-1485217988980-11786ced9454?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&h=528&q=80" alt=""
                                             class="aspect-2/3 w-full rounded-xl bg-gray-700/5 object-cover shadow-lg" />
                                        <div class="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-inset ring-white/10"></div>
                                    </div>
                                    <div class="relative">
                                        <img src="https://images.unsplash.com/photo-1559136555-9303baea8ebd?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&crop=focalpoint&fp-x=.4&w=396&h=528&q=80" alt=""
                                             class="aspect-2/3 w-full rounded-xl bg-gray-700/5 object-cover shadow-lg" />
                                        <div class="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-inset ring-white/10"></div>
                                    </div>
                                </div>
                                <div class="w-44 flex-none space-y-8 pt-32 sm:pt-0">
                                    <div class="relative">
                                        <img src="https://images.unsplash.com/photo-1670272504528-790c24957dda?ixlib=rb-4.0.3&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&crop=left&w=400&h=528&q=80" alt=""
                                             class="aspect-2/3 w-full rounded-xl bg-gray-700/5 object-cover shadow-lg" />
                                        <div class="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-inset ring-white/10"></div>
                                    </div>
                                    <div class="relative">
                                        <img src="https://images.unsplash.com/photo-1670272505284-8faba1c31f7d?ixlib=rb-4.0.3&ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&h=528&q=80" alt=""
                                             class="aspect-2/3 w-full rounded-xl bg-gray-700/5 object-cover shadow-lg" />
                                        <div class="pointer-events-none absolute inset-0 rounded-xl ring-1 ring-inset ring-white/10"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="mx-auto max-w-7xl px-4 py-24 text-center sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-zinc-900 dark:text-white">{{ __('Welcome') }}</h1>
            <flux:text class="mt-4 text-zinc-600 dark:text-zinc-400">{{ __('Content coming soon...') }}</flux:text>
        </div>
    @endif
</div>
