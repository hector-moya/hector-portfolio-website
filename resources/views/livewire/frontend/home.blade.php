<div>
    @if ($entry)
        {{-- Hero Section --}}
        <div class="relative bg-gradient-to-r from-indigo-900 to-purple-900 text-white">
            <div class="relative isolate">
                <svg aria-hidden="true" class="h-256 mask-[radial-gradient(32rem_32rem_at_center,white,transparent)] absolute inset-x-0 top-0 -z-10 w-full stroke-white/10">
                    <defs>
                        <pattern id="1f932ae7-37de-4c0a-a8b0-a6e3b4d44b84" width="200" height="200" x="50%" y="-1" patternUnits="userSpaceOnUse">
                            <path d="M.5 200V.5H200" fill="none" />
                        </pattern>
                    </defs>
                    <svg x="50%" y="-1" class="overflow-visible fill-gray-800">
                        <path d="M-200 0h201v201h-201Z M600 0h201v201h-201Z M-400 600h201v201h-201Z M200 800h201v201h-201Z" stroke-width="0" />
                    </svg>
                    <rect width="100%" height="100%" fill="url(#1f932ae7-37de-4c0a-a8b0-a6e3b4d44b84)" stroke-width="0" />
                </svg>
                <div aria-hidden="true" class="absolute left-1/2 right-0 top-0 -z-10 -ml-24 transform-gpu overflow-hidden blur-3xl lg:ml-24 xl:ml-48">
                    <div style="clip-path: polygon(63.1% 29.5%, 100% 17.1%, 76.6% 3%, 48.4% 0%, 44.6% 4.7%, 54.5% 25.3%, 59.8% 49%, 55.2% 57.8%, 44.4% 57.2%, 27.8% 47.9%, 35.1% 81.5%, 0% 97.7%, 39.2% 100%, 35.2% 81.4%, 97.2% 52.8%, 63.1% 29.5%)"
                         class="aspect-801/1036 w-200.25 bg-linear-to-tr from-[#ff80b5] to-[#9089fc] opacity-30"></div>
                </div>
                <div class="overflow-hidden">
                    <div class="mx-auto max-w-7xl px-6 pb-32 pt-36 sm:pt-60 lg:px-8 lg:pt-32">
                        <div class="mx-auto max-w-2xl gap-x-14 lg:mx-0 lg:flex lg:max-w-none lg:items-center">
                            <div class="relative w-full lg:max-w-xl lg:shrink-0 xl:max-w-2xl space-y-6">
                                <flux:heading class="!text-6xl font-bold" level="1">
                                    {{ $entry->elements->firstWhere('handle', 'hero_title')?->value ?? 'Welcome' }}
                                </flux:heading>
                                <flux:text class="text-base">{{ $entry->elements->firstWhere('handle', 'hero_subtitle')?->value ?? 'Your subtitle here' }}</flux:text>
                                <flux:text class="text-base">{{ $entry->elements->firstWhere('handle', 'content')?->value ?? 'Your content here' }}</flux:text>
                                <div class="mt-10 flex items-center gap-x-6">
                                    <flux:button variant="primary" href="{{ $entry->elements->firstWhere('handle', 'cta_url')?->value ?? '#' }}"
                                       class="shadow-xs rounded-md bg-indigo-500 px-3.5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                                       {{ $entry->elements->firstWhere('handle', 'cta_text')?->value }}
                                    </flux:button>
                                    <flux:button href="{{ $entry->elements->firstWhere('handle', 'cta_url_1')?->value ?? '#' }}" class="text-sm/6 font-semibold text-white">
                                       {{ $entry->elements->firstWhere('handle', 'cta_text_1')?->value }}
                                    </flux:button>
                                </div>
                            </div>
                            <div class="mt-14 flex justify-end gap-8 sm:-mt-44 sm:justify-start sm:pl-20 lg:mt-0 lg:pl-0">
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
            <h1 class="text-4xl font-bold text-zinc-900 dark:text-white">Welcome</h1>
            <flux:text class="mt-4 text-zinc-600 dark:text-zinc-400">Content coming soon...</flux:text>
        </div>
    @endif
</div>
