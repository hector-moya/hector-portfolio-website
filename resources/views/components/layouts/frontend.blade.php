<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
    <script>
        // Apply saved theme before render to prevent flash
        (function () {
            if (localStorage.getItem('theme') === 'light') {
                document.documentElement.classList.remove('dark');
            } else {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>

<body
    x-data="{
        scrolled: false,
        mobileOpen: false,
        dark: document.documentElement.classList.contains('dark'),
        toggleDark() {
            this.dark = !this.dark;
            document.documentElement.classList.toggle('dark', this.dark);
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
        }
    }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 40 }, { passive: true })"
    class="flex min-h-screen flex-col justify-between bg-white font-sans antialiased dark:bg-zinc-900"
>
    @php
        $siteName = \App\Facades\Globals::get('branding.site_name.content', config('app.name')) ?: config('app.name');
        $logoAssetId = \App\Facades\Globals::get('branding.logo.image');
        $logoAsset = $logoAssetId ? \App\Models\Asset::find($logoAssetId) : null;
    @endphp

    <!-- Navigation -->
    <header
        :class="scrolled
            ? 'bg-gray-50 backdrop-blur-md shadow-sm dark:bg-zinc-900/90 border-b border-zinc-200/60 dark:border-zinc-700/60'
            : 'bg-transparent'"
        class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
    >
        <nav aria-label="Global" class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
            <!-- Logo -->
            <div class="flex lg:flex-1">
                <a
                    href="/"
                    class="-m-1.5 p-1.5 transition-opacity hover:opacity-75"
                    :class="scrolled ? 'text-zinc-900 dark:text-white' : 'text-white'"
                >
                    @if ($logoAsset)
                        <img src="{{ $logoAsset->url }}" alt="{{ $siteName }}" class="h-8 w-auto" />
                    @else
                        <span class="text-xl font-semibold transition-colors duration-300">
                            {{ $siteName }}
                        </span>
                    @endif
                </a>
            </div>

            <!-- Mobile: dark toggle + hamburger -->
            <div class="flex items-center gap-1 lg:hidden">
                <button
                    @click="toggleDark()"
                    class="rounded-full p-2 transition-colors"
                    :class="scrolled
                        ? 'text-zinc-700 hover:bg-zinc-100 dark:text-zinc-300 dark:hover:bg-zinc-800'
                        : 'text-white hover:bg-white/10'"
                    aria-label="{{ __('Toggle dark mode') }}"
                >
                    <flux:icon x-show="dark" name="sun" class="size-5" />
                    <flux:icon x-show="!dark" name="moon" class="size-5" />
                </button>

                <button
                    @click="mobileOpen = true"
                    class="rounded-md p-2 transition-colors"
                    :class="scrolled
                        ? 'text-zinc-700 dark:text-zinc-300'
                        : 'text-white'"
                    aria-label="{{ __('Open menu') }}"
                >
                    <flux:icon name="bars-3" class="size-6" />
                </button>
            </div>

            <!-- Desktop nav -->
            @php $mainMenu = \App\Facades\Navigation::get('main-menu'); @endphp
            <div
                class="hidden lg:flex lg:items-center lg:gap-x-2"
                :class="scrolled ? 'text-teal-600 dark:text-teal-300' : 'text-teal-700 dark:text-teal-600'"
            >
                <x-menu :navigation="$mainMenu" class="text-sm/6 font-semibold" />

                <div class="ml-4 h-5 w-px bg-current opacity-20"></div>

                <!-- Dark mode toggle desktop -->
                <button
                    @click="toggleDark()"
                    class="rounded-full p-2 transition-colors hover:bg-black/10 dark:hover:bg-white/10"
                    aria-label="{{ __('Toggle dark mode') }}"
                >
                    <flux:icon x-show="dark" name="sun" class="size-5" />
                    <flux:icon x-show="!dark" name="moon" class="size-5" />
                </button>
            </div>
        </nav>
    </header>

    <!-- Mobile Menu Overlay -->
    <div
        x-show="mobileOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @keydown.escape.window="mobileOpen = false"
        class="fixed inset-0 z-50 lg:hidden"
        style="display: none"
    >
        <!-- Backdrop -->
        <div
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            @click="mobileOpen = false"
        ></div>

        <!-- Slide-in panel -->
        <div
            x-show="mobileOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="absolute inset-y-0 right-0 w-full overflow-y-auto bg-zinc-950 px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-white/10"
            style="display: none"
        >
            <div class="flex items-center justify-between">
                <a href="/" class="-m-1.5 p-1.5">
                    <span class="text-xl font-semibold text-white">{{ $siteName }}</span>
                </a>
                <button
                    @click="mobileOpen = false"
                    class="-m-2.5 rounded-md p-2.5 text-zinc-400 hover:text-zinc-300 transition-colors"
                >
                    <span class="sr-only">{{ __('Close menu') }}</span>
                    <flux:icon name="x-mark" class="size-6" />
                </button>
            </div>

            <div class="mt-8 flow-root">
                <div class="-my-4 divide-y divide-white/10">
                    <div class="py-4">
                        <x-menu :navigation="$mainMenu" class="flex-col text-base font-semibold text-white" />
                    </div>
                    <div class="py-4">
                        <button
                            @click="toggleDark(); mobileOpen = false"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-semibold text-zinc-300 hover:bg-white/5 hover:text-white transition-colors"
                        >
                            <flux:icon x-show="dark" name="sun" class="size-5" />
                            <flux:icon x-show="!dark" name="moon" class="size-5" />
                            <span x-text="dark ? '{{ __('Switch to light mode') }}' : '{{ __('Switch to dark mode') }}'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-6 sm:flex-row">
                <a href="/" class="text-sm font-semibold text-zinc-900 transition-opacity hover:opacity-75 dark:text-white">
                    {{ $siteName }}
                </a>
                <x-menu
                    :navigation="\App\Facades\Navigation::get('footer-menu')"
                    class="flex-wrap justify-center gap-6 text-sm text-zinc-500 dark:text-zinc-400"
                />
            </div>
            <div class="mt-8 border-t border-zinc-200 pt-6 dark:border-zinc-800">
                <p class="text-center text-xs text-zinc-400 dark:text-zinc-500">
                    &copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved') }}.
                </p>
            </div>
        </div>
    </footer>

    <!-- Scroll-reveal observer -->
    <script>
        function initScrollReveal() {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('in-view');
                            observer.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.1, rootMargin: '0px 0px -48px 0px' }
            );
            document.querySelectorAll('[data-animate]:not(.in-view)').forEach(el => observer.observe(el));
        }

        document.addEventListener('DOMContentLoaded', initScrollReveal);
        document.addEventListener('livewire:navigated', initScrollReveal);
    </script>

    @fluxScripts
</body>

</html>
