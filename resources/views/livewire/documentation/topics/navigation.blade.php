<div class="space-y-8">

    {{-- Title --}}
    <div>
        <div class="flex items-center gap-3">
            <flux:icon.bars-3 variant="outline" class="size-8 text-zinc-400" />
            <flux:heading size="xl">{{ __('Navigation') }}</flux:heading>
        </div>
        <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
            {{ __('Build and manage hierarchical navigation menus for your website.') }}
        </flux:text>
    </div>

    <flux:separator />

    {{-- What is Navigation --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('What is Navigation?') }}</flux:heading>
        <flux:text class="mb-3">
            {{ __('Navigation is a menu builder that lets you create and manage named navigation menus. Each menu is made up of items — links that can point to any URL or to content within the CMS. Items can be nested to create dropdown or multi-level menus.') }}
        </flux:text>
        <flux:text class="mb-3">
            {{ __('A navigation menu has a handle (e.g.') }}
            <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">main-menu</code>
            {{ __(') that your frontend templates use to load and render it. Menus can be set to Active or Draft — only active menus are returned by default when rendering on the frontend.') }}
        </flux:text>
        <flux:text>
            {{ __('Navigation menus are cached aggressively for frontend performance. The cache is automatically invalidated whenever a menu or any of its items is created, updated, reordered, or deleted.') }}
        </flux:text>
    </div>

    {{-- How to Access --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('How to Access') }}</flux:heading>
        <flux:text class="mb-3">{{ __('Navigation is managed from the admin sidebar:') }}</flux:text>
        <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
            <li class="flex items-start gap-2">
                <flux:icon.arrow-right class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span>
                    <strong>{{ __('Sidebar') }}</strong> — {{ __('Navigate to') }}
                    <flux:badge size="sm" color="zinc">{{ __('Navigation') }}</flux:badge>
                    {{ __('to see a list of all navigation menus.') }}
                </span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.arrow-right class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span>
                    <strong>{{ __('Edit page') }}</strong> — {{ __('Click the edit button on any menu to manage its settings and items.') }}
                </span>
            </li>
        </ul>
    </div>

    {{-- Current Features --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('Current Features') }}</flux:heading>

        <div class="space-y-6">

            {{-- Menu Settings --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Menu Settings') }}</flux:heading>
                <flux:text class="mb-3">{{ __('Each navigation menu has the following settings:') }}</flux:text>
                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-zinc-700 dark:text-zinc-300">{{ __('Field') }}</th>
                                <th class="px-4 py-2 text-left font-medium text-zinc-700 dark:text-zinc-300">{{ __('Description') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Name') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('A human-readable label shown in the CMS, e.g. "Main Menu".') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Handle') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('A unique, slug-style identifier used to load the menu in templates, e.g.') }} <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">main-menu</code>. {{ __('Auto-generated from the name on create but editable.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Description') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('Optional note about where or how this menu is used.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Status') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('Active or Draft. Only Active menus are returned when loading on the frontend.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Menu Items --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Menu Items') }}</flux:heading>
                <flux:text class="mb-3">{{ __('Each item within a menu has the following fields:') }}</flux:text>
                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-zinc-700 dark:text-zinc-300">{{ __('Field') }}</th>
                                <th class="px-4 py-2 text-left font-medium text-zinc-700 dark:text-zinc-300">{{ __('Description') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Title') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('The visible link text rendered in the menu.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('URL') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('The link destination. Can be an absolute URL, a relative path, or an anchor.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Target') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('Controls how the link opens: same window (') }}<code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">_self</code>{{ __(') or new tab (') }}<code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">_blank</code>{{ __(').') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('CSS Class') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('Optional custom CSS classes applied to the rendered link element.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Parent') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('Items can be nested under a parent item to create dropdown menus. Set automatically by drag-and-drop positioning.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Adding & Editing Items --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Adding & Editing Items') }}</flux:heading>
                <flux:text class="mb-3">{{ __('On the menu edit page, the right panel contains the menu item builder:') }}</flux:text>
                <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <li class="flex items-start gap-2">
                        <flux:icon.plus class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('Add item') }}</strong> — {{ __('Enter a title and URL in the "Add Item" form at the top of the panel and click "Add". The new item appears at the bottom of the list.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.pencil class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('Edit item') }}</strong> — {{ __('Click the pencil icon next to any item to enter inline edit mode. Update the title or URL and click "Save".') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.trash class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('Delete item') }}</strong> — {{ __('Click the trash icon to remove an item. A confirmation prompt will appear before the item is deleted.') }}</span>
                    </li>
                </ul>
            </div>

            {{-- Drag-and-Drop Reordering --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Drag-and-Drop Reordering') }}</flux:heading>
                <flux:text class="mb-3">
                    {{ __('Menu items can be reordered by dragging them using the handle icon (') }}<flux:icon.chevron-up-down class="inline size-4 text-zinc-400" />{{ __(') on the left side of each row. The order is saved automatically after each drag.') }}
                </flux:text>
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Reordering also updates parent-child relationships — dragging an item under another item makes it a child (nested dropdown) of that item.') }}
                </flux:text>
            </div>

            {{-- Status & Search --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Menu List — Search & Status') }}</flux:heading>
                <flux:text class="mb-3">{{ __('The Navigation index page provides:') }}</flux:text>
                <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <li class="flex items-start gap-2">
                        <flux:icon.magnifying-glass class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('Search') }}</strong> — {{ __('Filter menus by name or handle. Results update as you type (300ms debounce).') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.tag class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('Status badge') }}</strong> — {{ __('Each menu in the list shows an') }} <flux:badge size="sm" color="green">{{ __('Active') }}</flux:badge> {{ __('or') }} <flux:badge size="sm" color="zinc">{{ __('Draft') }}</flux:badge> {{ __('badge.') }}</span>
                    </li>
                </ul>
            </div>

            {{-- Frontend Rendering --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Frontend Rendering') }}</flux:heading>
                <flux:text class="mb-3">
                    {{ __('Navigation menus are rendered on the frontend using the') }}
                    <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">&lt;x-menu&gt;</code>
                    {{ __('Blade component. Pass the') }}
                    <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">handle</code>
                    {{ __('attribute to specify which menu to render:') }}
                </flux:text>
                <div class="rounded-lg bg-zinc-900 px-4 py-3 font-mono text-sm text-zinc-100 dark:bg-zinc-800">
                    &lt;x-menu handle="main-menu" /&gt;
                </div>
                <flux:text class="mt-3 mb-3 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('The component automatically:') }}
                </flux:text>
                <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <li class="flex items-start gap-2">
                        <flux:icon.check class="mt-0.5 size-4 shrink-0 text-green-500" />
                        <span>{{ __('Renders top-level items as links, and items with children as dropdown triggers.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.check class="mt-0.5 size-4 shrink-0 text-green-500" />
                        <span>{{ __('Highlights the active link based on the current page URL.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.check class="mt-0.5 size-4 shrink-0 text-green-500" />
                        <span>{{ __('Applies any custom CSS classes set on individual items.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.check class="mt-0.5 size-4 shrink-0 text-green-500" />
                        <span>{{ __('Supports dark mode via Tailwind') }} <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">dark:</code> {{ __('variants.') }}</span>
                    </li>
                </ul>

                <flux:text class="mt-4 mb-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ __('Loading a menu in PHP') }}</flux:text>
                <flux:text class="mb-2 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('You can also retrieve a navigation menu programmatically using the') }}
                    <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">Navigation</code>
                    {{ __('facade:') }}
                </flux:text>
                <div class="rounded-lg bg-zinc-900 px-4 py-3 font-mono text-sm text-zinc-100 dark:bg-zinc-800">
                    {{ __('use App\Facades\Navigation;') }}<br /><br />
                    $menu = Navigation::get('main-menu');<br />
                    $items = $menu->items; // {{ __('ordered NavigationItem collection') }}
                </div>
            </div>

            {{-- Caching --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Caching') }}</flux:heading>
                <flux:text class="mb-3">
                    {{ __('Navigation menus are cached indefinitely to avoid repeated database queries on every page load. The cache is automatically cleared whenever:') }}
                </flux:text>
                <ul class="space-y-1 text-sm text-zinc-700 dark:text-zinc-300">
                    <li class="flex items-start gap-2">
                        <flux:icon.arrow-right class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span>{{ __('A menu\'s settings are saved.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.arrow-right class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span>{{ __('An item is added, edited, deleted, or reordered.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.arrow-right class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span>{{ __('A new menu is created or an existing one is deleted.') }}</span>
                    </li>
                </ul>
                <flux:text class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('You do not need to manually clear the cache. All cache invalidation is handled automatically by the CMS.') }}
                </flux:text>
            </div>

        </div>
    </div>

    {{-- Planned Future Features --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('Planned Future Features') }}</flux:heading>
        <flux:text class="mb-4">{{ __('The following features are planned for future releases:') }}</flux:text>
        <ul class="space-y-3 text-sm text-zinc-700 dark:text-zinc-300">
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Link to CMS entries') }}</strong> — {{ __('Pick an entry directly from the CMS as a menu item destination, so URLs update automatically when an entry\'s slug changes.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Unlimited nesting depth') }}</strong> — {{ __('Support for deeply nested menu structures beyond the current parent-child level.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Item icons') }}</strong> — {{ __('Assign an icon to individual menu items for icon-based navigation layouts.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Visibility rules') }}</strong> — {{ __('Show or hide menu items based on conditions such as user authentication state or role.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Clone menu') }}</strong> — {{ __('Duplicate an existing menu as a starting point for a new one.') }}</span>
            </li>
        </ul>
    </div>

</div>
