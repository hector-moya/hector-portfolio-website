@props(['navigation' => null, 'class' => ''])

@if ($navigation)
    <nav
        @class([
            'flex gap-8',
            $class,
        ])
    >
        @foreach ($navigation->items->whereNull('parent_id') as $item)
            <a
                href="{{ $item->url }}"
                @class([
                    'inline-flex items-center gap-2 transition',
                    'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200' => !request()->is(trim($item->url, '/')),
                    'text-gray-900 dark:text-white' => request()->is(trim($item->url, '/')),
                ])
            >
                @if ($item->icon)
                    <flux:icon name="{{ $item->icon }}" class="h-5 w-5" />
                @endif

                <span>{{ $item->title }}</span>

                @if ($item->children->count())
                    <flux:icon name="chevron-down" class="h-4 w-4" />
                @endif
            </a>

            @if ($item->children->count())
                <div class="relative">
                    <div class="absolute left-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-zinc-800" role="menu">
                        @foreach ($item->children as $child)
                            <a
                                href="{{ $child->url }}"
                                @class([
                                    'block px-4 py-2 text-sm transition',
                                    'text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200' => !request()->is(trim($child->url, '/')),
                                    'text-gray-900 dark:text-white' => request()->is(trim($child->url, '/')),
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
