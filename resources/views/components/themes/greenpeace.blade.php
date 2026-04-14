<div class="relative overflow-x-hidden bg-linear-to-br from-teal-950 via-zinc-900 to-green-950">
    {{-- Decorative elements (only visible through transparent sections like the hero) --}}
    <div class="pointer-events-none absolute inset-x-0 top-0 z-0 h-screen overflow-hidden" aria-hidden="true">
        <div class="animate-float-slow absolute -right-32 -top-32 h-120 w-120 rounded-full bg-teal-500/20 blur-3xl"></div>
        <div class="animate-float-slower absolute -bottom-32 -left-32 h-90 w-90 rounded-full bg-green-500/15 blur-3xl"></div>
        <div class="animate-pulse-slow absolute left-1/2 top-1/3 h-125 w-125 -translate-x-1/2 rounded-full bg-teal-400/10 blur-3xl"></div>

        <svg aria-hidden="true" class="absolute inset-0 h-full w-full stroke-white/5" viewBox="0 0 800 600" preserveAspectRatio="none">
            <defs>
                <pattern id="gp-grid" width="80" height="80" patternUnits="userSpaceOnUse">
                    <path d="M 80 0 L 0 0 0 80" fill="none" stroke-width="0.5" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#gp-grid)" />
        </svg>
    </div>

    {{ $slot }}
</div>
