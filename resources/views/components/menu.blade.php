@props(['navigation' => null, 'handle' => null, 'class' => ''])

@php
    if ($navigation === null && $handle !== null) {
        $navigation = \App\Models\Navigation::with(['items.children'])->where('handle', $handle)->first();
    }
@endphp

@if ($navigation)
    <nav
        @class([
            'flex gap-6',
            $class,
        ])
    >
        @foreach ($navigation->items->whereNull('parent_id') as $item)
            <a
                href="{{ $item->url }}"
                wire:navigate
                @class([
                    'sp-nav-link inline-flex items-center gap-1.5 transition-opacity duration-150',
                    'opacity-60 hover:opacity-100' => ! request()->is(trim($item->url, '/')),
                    'opacity-100 font-semibold' => request()->is(trim($item->url, '/')),
                ])
            >
                @if ($item->icon)
                    <flux:icon name="{{ $item->icon }}" class="size-4" />
                @endif

                <span>{{ $item->title }}</span>

                @if ($item->children->count())
                    <flux:icon name="chevron-down" class="size-3.5" />
                @endif
            </a>

            @if ($item->children->count())
                <div class="relative">
                    <div class="absolute left-0 z-10 mt-2 w-52 origin-top-left rounded-xl border border-zinc-200/80 bg-white/95 shadow-lg backdrop-blur-sm dark:border-zinc-700/80 dark:bg-zinc-800/95" role="menu">
                        @foreach ($item->children as $child)
                            <a
                                href="{{ $child->url }}"
                                wire:navigate
                                @class([
                                    'sp-nav-link block px-4 py-2.5 text-sm transition-opacity first:rounded-t-xl last:rounded-b-xl',
                                    'opacity-60 hover:opacity-100 hover:bg-zinc-50 dark:hover:bg-zinc-700/50' => ! request()->is(trim($child->url, '/')),
                                    'opacity-100 font-medium bg-zinc-50 dark:bg-zinc-700/50' => request()->is(trim($child->url, '/')),
                                ])
                                role="menuitem"
                            >
                                {{ $child->title }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </nav>
@endif
