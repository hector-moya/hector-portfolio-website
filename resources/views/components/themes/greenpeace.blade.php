<div>
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
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
