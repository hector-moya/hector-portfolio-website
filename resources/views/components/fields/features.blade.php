@props(['section', 'assets' => collect()])

@php
    $data  = $section['data'];
    $items = $data['items'] ?? [];

    // Cycle accent colours: solar → bio → leaf
    $accents = [
        ['var'  => '--sp-solar', 'glow' => '--sp-glow-s'],
        ['var'  => '--sp-bio',   'glow' => '--sp-glow-b'],
        ['var'  => '--sp-leaf',  'glow' => '--sp-glow-l'],
    ];
@endphp

@if (!empty($data['title']) || !empty($items))
    <div class="relative overflow-hidden py-16 sm:py-24" style="background: var(--sp-bg);">

        {{-- Decorative accent blob --}}
        <div class="pointer-events-none absolute -bottom-20 -right-20 h-80 w-80 rounded-full opacity-20 blur-3xl dark:opacity-10" style="background: var(--sp-bio);" aria-hidden="true"></div>

        <div class="relative z-10 mx-auto max-w-7xl px-6 lg:px-8">

            {{-- Section header --}}
            @if (!empty($data['title']) || !empty($data['eyebrow']))
                <div data-animate class="mx-auto max-w-2xl text-center">
                    @if (!empty($data['eyebrow']))
                        <div class="mb-3 text-[0.68rem] font-semibold uppercase tracking-[0.28em]" style="color: var(--sp-bio); text-shadow: 0 0 12px var(--sp-glow-b);">
                            {{ $data['eyebrow'] }}
                        </div>
                    @endif
                    @if (!empty($data['title']))
                        <h2 class="text-[clamp(1.8rem,3vw,2.6rem)] font-semibold" style="font-family: 'Cinzel', serif; color: var(--sp-fg);">
                            {{ $data['title'] }}
                        </h2>
                    @endif
                </div>
            @endif

            {{-- Cards grid --}}
            @if (!empty($items))
                <div @class([
                    'mt-12 grid gap-5',
                    'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3' => count($items) > 2,
                    'grid-cols-1 sm:grid-cols-2'               => count($items) === 2,
                    'mx-auto max-w-md grid-cols-1'             => count($items) === 1,
                ])>
                    @foreach ($items as $i => $item)
                        @php
                            $accent = $accents[$i % 3];
                            $delay  = min(($i + 1) * 100, 500);
                        @endphp
                        <div
                            data-animate
                            data-delay="{{ $delay }}"
                            class="group relative flex flex-col gap-5 overflow-hidden rounded-xl border p-6 transition-all duration-300 hover:-translate-y-1.5"
                            style="background: var(--sp-bg-card); border-color: var(--sp-border);"
                            onmouseover="
                                this.style.borderColor = 'oklch(from var({{ $accent['var'] }}) l c h / 0.5)';
                                this.style.boxShadow   = '0 0 32px oklch(from var({{ $accent['var'] }}) l c h / 0.18), 0 12px 40px oklch(0 0 0 / 0.25)';
                            "
                            onmouseout="
                                this.style.borderColor = 'var(--sp-border)';
                                this.style.boxShadow   = 'none';
                            "
                        >
                            {{-- Top-edge highlight (appears on hover via CSS sibling trick via JS above) --}}
                            <div class="absolute left-[10%] right-[10%] top-0 h-px opacity-0 transition-opacity duration-300 group-hover:opacity-100" style="background: linear-gradient(90deg, transparent, var({{ $accent['var'] }}), transparent);"></div>

                            {{-- Icon --}}
                            @if (!empty($item['icon']))
                                @php $iconName = Str::kebab($item['icon']); @endphp
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl" style="background: oklch(from var({{ $accent['var'] }}) l c h / 0.12); box-shadow: 0 0 18px oklch(from var({{ $accent['var'] }}) l c h / 0.25); color: var({{ $accent['var'] }});">
                                    @if (view()->exists('flux::icon.' . $iconName))
                                        <flux:icon :name="$iconName" class="size-5" />
                                    @else
                                        <flux:icon name="sparkles" class="size-5" />
                                    @endif
                                </div>
                            @endif

                            {{-- Title --}}
                            @if (!empty($item['item_title']))
                                <h3 class="text-base font-semibold leading-snug" style="font-family: 'Cinzel', serif; color: var(--sp-fg);">
                                    {{ $item['item_title'] }}
                                </h3>
                            @endif

                            {{-- Description --}}
                            @if (!empty($item['item_description']))
                                <p class="text-sm leading-7" style="color: var(--sp-muted);">
                                    {{ $item['item_description'] }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

    <div class="relative z-10" style="height: 1px; background: linear-gradient(90deg, transparent, var(--sp-border), transparent);"></div>
@endif
