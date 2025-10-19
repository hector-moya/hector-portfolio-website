<div>
    <x-themes.greenpeace>
    @if ($entry)
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
    @else
        <div class="mx-auto max-w-7xl px-4 py-24 text-center sm:px-6 lg:px-8 space-y-8">
            <flux:heading class="!text-4xl">{{ __('Welcome') }}</flux:heading>
            <flux:text >{{ __('Content coming soon...') }}</flux:text>
        </div>
    @endif
    </x-themes.greenpeace>
</div>
