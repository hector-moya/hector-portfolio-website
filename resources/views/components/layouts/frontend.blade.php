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
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { dark: this.dark } }));
        }
    }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 40 }, { passive: true })"
    class="sp-site-body flex min-h-screen flex-col justify-between antialiased"
>
    {{-- Skip to main content (keyboard accessibility) --}}
    <a
        href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[100] focus:rounded focus:px-4 focus:py-2 focus:text-sm focus:font-semibold"
        style="background: var(--sp-solar); color: var(--sp-bg);"
    >
        {{ __('Skip to content') }}
    </a>

    {{-- Particle canvas — dark mode bioluminescent spores --}}
    <canvas id="sp-particles" aria-hidden="true"></canvas>

    {{-- Topographic contour lines — light mode only --}}
    <svg id="sp-topo-fixed" viewBox="0 0 1440 900" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
        <path d="M-100 780 Q300 720 700 755 Q1100 790 1540 740" fill="none" stroke="oklch(0.55 0.18 82)" stroke-width="1.0" opacity="0.5"/>
        <path d="M-100 820 Q280 760 680 795 Q1080 830 1540 778" fill="none" stroke="oklch(0.55 0.18 82)" stroke-width="0.7" opacity="0.4"/>
        <path d="M-100 860 Q320 800 720 835 Q1120 870 1540 815" fill="none" stroke="oklch(0.55 0.18 82)" stroke-width="0.5" opacity="0.3"/>
        <path d="M-100 710 Q350 640 750 678 Q1150 715 1540 665" fill="none" stroke="oklch(0.45 0.14 202)" stroke-width="0.8" opacity="0.4"/>
        <path d="M-100 640 Q400 570 800 610 Q1200 650 1540 595" fill="none" stroke="oklch(0.45 0.14 202)" stroke-width="0.5" opacity="0.3"/>
    </svg>

    @php
        $siteName = \App\Facades\Globals::get('branding.site_name.content', config('app.name')) ?: config('app.name');
        $logoAssetId = \App\Facades\Globals::get('branding.logo.image');
        $logoAsset = $logoAssetId ? \App\Models\Asset::find($logoAssetId) : null;
    @endphp

    <!-- Navigation -->
    <header
        :class="scrolled ? 'sp-nav-scrolled border-b' : ''"
        class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
    >
        <nav aria-label="Global" class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4 lg:px-8">
            <!-- Logo -->
            <div class="flex lg:flex-1">
                <a href="/" wire:navigate class="sp-nav-link -m-1.5 p-1.5 transition-opacity hover:opacity-80">
                    @if ($logoAsset)
                        <img src="{{ $logoAsset->url }}" alt="{{ $siteName }}" class="h-8 w-auto" />
                    @else
                        <span class="text-lg tracking-widest transition-all duration-300" style="font-family: 'Cinzel', serif; color: var(--sp-solar); text-shadow: 0 0 18px var(--sp-glow-s);">
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
                    style="color: var(--sp-muted);"
                    aria-label="{{ __('Toggle dark mode') }}"
                >
                    <flux:icon x-show="dark" name="sun" class="size-5" />
                    <flux:icon x-show="!dark" name="moon" class="size-5" />
                </button>

                <button
                    @click="mobileOpen = true"
                    class="rounded-md p-2 transition-colors"
                    style="color: var(--sp-muted);"
                    aria-label="{{ __('Open menu') }}"
                >
                    <flux:icon name="bars-3" class="size-6" />
                </button>
            </div>

            <!-- Desktop nav -->
            @php $mainMenu = \App\Facades\Navigation::get('main-menu'); @endphp
            <div class="hidden items-center gap-x-2 lg:flex" style="color: var(--sp-muted);">
                <x-menu :navigation="$mainMenu" class="text-xs font-medium uppercase tracking-widest" />

                <div class="ml-4 h-5 w-px opacity-20" style="background: var(--sp-border);"></div>

                <!-- Dark mode toggle desktop -->
                <button
                    @click="toggleDark()"
                    class="rounded-full border p-2 transition-all duration-300 hover:opacity-100"
                    style="border-color: var(--sp-border); color: var(--sp-muted);"
                    :style="dark ? 'box-shadow: 0 0 14px var(--sp-glow-s);' : ''"
                    aria-label="{{ __('Toggle dark mode') }}"
                >
                    <flux:icon x-show="dark" name="sun" class="size-4" />
                    <flux:icon x-show="!dark" name="moon" class="size-4" />
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
        role="dialog"
        aria-modal="true"
        aria-label="{{ __('Site menu') }}"
    >
        <!-- Backdrop -->
        <div
            class="absolute inset-0 bg-black/60 backdrop-blur-sm"
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
            class="absolute inset-y-0 right-0 w-full overflow-y-auto px-6 py-6 sm:max-w-sm sm:ring-1"
            style="background: var(--sp-bg-mid); ring-color: var(--sp-border); display: none"
        >
            <div class="flex items-center justify-between">
                <a href="/" class="-m-1.5 p-1.5">
                    <span class="text-lg tracking-widest" style="font-family: 'Cinzel', serif; color: var(--sp-solar);">{{ $siteName }}</span>
                </a>
                <button
                    @click="mobileOpen = false"
                    class="-m-2.5 rounded-md p-2.5 transition-colors"
                    style="color: var(--sp-muted);"
                >
                    <span class="sr-only">{{ __('Close menu') }}</span>
                    <flux:icon name="x-mark" class="size-6" />
                </button>
            </div>

            <div class="mt-8 flow-root">
                <div class="-my-4 divide-y" style="border-color: var(--sp-border);">
                    <div class="py-4">
                        <x-menu :navigation="$mainMenu" class="flex-col text-base font-medium uppercase tracking-widest" style="color: var(--sp-fg);" />
                    </div>
                    <div class="py-4">
                        <button
                            @click="toggleDark(); mobileOpen = false"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors hover:opacity-80"
                            style="color: var(--sp-muted);"
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
    <main id="main-content" tabindex="-1" class="relative z-10 scroll-mt-24 focus:outline-none">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="relative z-10 border-t" style="background: var(--sp-bg); border-color: var(--sp-border);">
        <div class="mx-auto max-w-7xl px-6 pb-8 pt-12 lg:px-8">
            <div class="flex flex-col items-center justify-between gap-6 sm:flex-row">
                <a href="/" wire:navigate class="sp-nav-link text-sm font-medium tracking-widest transition-all hover:opacity-80" style="font-family: 'Cinzel', serif; color: var(--sp-solar); text-shadow: 0 0 12px var(--sp-glow-s);">
                    {{ $siteName }}
                </a>
                <x-menu
                    :navigation="\App\Facades\Navigation::get('footer-menu')"
                    class="flex-wrap justify-center gap-6 text-xs uppercase tracking-widest"
                    style="color: var(--sp-muted);"
                />
            </div>
        </div>
        <div class="border-t py-6" style="border-color: var(--sp-border);">
            <p class="text-center text-xs tracking-wide" style="color: var(--sp-muted);">
                &copy; {{ date('Y') }} <span style="color: var(--sp-leaf);">{{ $siteName }}</span>. {{ __('All rights reserved') }}.
            </p>
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

    <!-- Solarpunk particle system (dark mode bioluminescent spores) -->
    <script>
        (function () {
            const canvas = document.getElementById('sp-particles');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');

            function resizeCanvas() {
                canvas.width  = window.innerWidth;
                canvas.height = document.body.scrollHeight;
            }

            const COLS = [
                'oklch(0.78 0.19 82)',
                'oklch(0.72 0.17 178)',
                'oklch(0.65 0.20 138)',
            ];

            class Spore {
                constructor() { this.reset(true); }
                reset(init) {
                    this.x      = Math.random() * canvas.width;
                    this.y      = init ? Math.random() * canvas.height : canvas.height + 10;
                    this.r      = Math.random() * 1.6 + 0.4;
                    this.vx     = (Math.random() - 0.5) * 0.25;
                    this.vy     = -(Math.random() * 0.45 + 0.18);
                    this.alpha  = Math.random() * 0.45 + 0.2;
                    this.pulse  = Math.random() * Math.PI * 2;
                    this.color  = COLS[Math.floor(Math.random() * 3)];
                    this.life   = 0;
                    this.maxLife = Math.random() * 700 + 300;
                }
                update() {
                    this.x += this.vx;
                    this.y += this.vy;
                    this.pulse += 0.018;
                    this.life++;
                    if (this.life > this.maxLife) this.reset(false);
                }
                draw() {
                    const a = this.alpha * (0.7 + 0.3 * Math.sin(this.pulse));
                    ctx.save();
                    ctx.globalAlpha = a;
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
                    ctx.fillStyle    = this.color;
                    ctx.shadowBlur   = 8;
                    ctx.shadowColor  = this.color;
                    ctx.fill();
                    ctx.restore();
                }
            }

            const spores = Array.from({ length: 85 }, () => new Spore());

            let rafId = null;

            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                spores.forEach(s => { s.update(); s.draw(); });
                rafId = requestAnimationFrame(animate);
            }

            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

            function shouldRun() {
                return document.documentElement.classList.contains('dark')
                    && !document.hidden
                    && !reduceMotion.matches;
            }

            function start() {
                if (rafId === null && shouldRun()) {
                    animate();
                }
            }

            function stop() {
                if (rafId !== null) {
                    cancelAnimationFrame(rafId);
                    rafId = null;
                }
            }

            function sync() {
                shouldRun() ? start() : stop();
            }

            resizeCanvas();
            window.addEventListener('resize', resizeCanvas, { passive: true });
            // Pause animation when the tab is hidden, the theme is light, or the
            // visitor prefers reduced motion — saving CPU and battery.
            document.addEventListener('visibilitychange', sync);
            window.addEventListener('theme-changed', sync);
            reduceMotion.addEventListener('change', sync);
            sync();
        })();
    </script>

    @fluxScripts
</body>

</html>
