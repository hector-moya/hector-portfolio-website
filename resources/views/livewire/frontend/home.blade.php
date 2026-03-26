<div>
    @if ($entry && !empty($layout))
        @foreach ($layout as $section)
            <x-dynamic-component
                :component="'sections.' . str_replace('_', '-', $section['type'])"
                :section="$section"
                :assets="$assets"
                wire:key="section-{{ $section['_id'] }}"
            />
        @endforeach
    @elseif ($entry)
        @php
            $heroTitle   = $entry->elements->firstWhere('handle', 'hero_title')?->value ?? '';
            $heroSubtitle = $entry->elements->firstWhere('handle', 'hero_subtitle')?->value ?? '';
            $content     = $entry->elements->firstWhere('handle', 'content')?->value ?? '';
            $ctaText1    = $entry->elements->firstWhere('handle', 'cta_text_primary')?->value ?? '';
            $ctaUrl1     = $entry->elements->firstWhere('handle', 'cta_url_primary')?->value ?? '#';
            $ctaText2    = $entry->elements->firstWhere('handle', 'cta_text_secondary')?->value ?? '';
            $ctaUrl2     = $entry->elements->firstWhere('handle', 'cta_url_secondary')?->value ?? '#';
        @endphp

        <x-themes.wrapper :theme="$theme">
            <div class="mx-auto max-w-3xl">
                {{-- Eyebrow --}}
                @if ($heroSubtitle)
                    <p class="animate-slide-in-from-top mb-6 font-mono text-sm font-medium tracking-widest text-teal-300 uppercase opacity-0">
                        {{ $heroSubtitle }}
                    </p>
                @endif

                {{-- Headline --}}
                @if ($heroTitle)
                    <h1 class="animate-slide-in-from-top font-bold leading-[1.1] opacity-0 [animation-delay:100ms]"
                        style="font-size: clamp(2.5rem, 5vw, 4.5rem);">
                        {{ $heroTitle }}
                    </h1>
                @endif

                {{-- Divider --}}
                <div class="animate-slide-in-from-left mt-10 mb-10 h-px w-16 bg-teal-400 opacity-0 [animation-delay:250ms]"></div>

                {{-- Content --}}
                @if ($content)
                    <div class="animate-slide-in-from-left max-w-2xl opacity-0 [animation-delay:300ms]">
                        <p class="text-lg leading-8 text-zinc-300">
                            {!! nl2br(e($content)) !!}
                        </p>
                    </div>
                @endif

                {{-- CTAs --}}
                @if ($ctaText1 || $ctaText2)
                    <div class="animate-slide-in-from-right mt-12 flex flex-wrap items-center gap-4 opacity-0 [animation-delay:450ms]">
                        @if ($ctaText1)
                            <flux:button variant="primary" href="{{ $ctaUrl1 }}" class="shadow-xl">
                                {{ $ctaText1 }}
                            </flux:button>
                        @endif
                        @if ($ctaText2)
                            <flux:button href="{{ $ctaUrl2 }}" class="!text-white shadow-xl">
                                {{ $ctaText2 }}
                            </flux:button>
                        @endif
                    </div>
                @endif
            </div>
        </x-themes.wrapper>
    @else
        <x-themes.wrapper :theme="$theme">
            <div class="mx-auto max-w-3xl space-y-8 text-center">
                <flux:heading class="!text-4xl">{{ __('Welcome') }}</flux:heading>
                <flux:text>{{ __('Content coming soon...') }}</flux:text>
            </div>
        </x-themes.wrapper>
    @endif
</div>
