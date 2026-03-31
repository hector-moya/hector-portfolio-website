<div class="space-y-8">

    {{-- Title --}}
    <div>
        <div class="flex items-center gap-3">
            <flux:icon.globe-alt variant="outline" class="size-8 text-zinc-400" />
            <flux:heading size="xl">{{ __('Globals') }}</flux:heading>
        </div>
        <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
            {{ __('Site-wide settings and variables shared across your entire website.') }}
        </flux:text>
    </div>

    <flux:separator />

    {{-- What are Globals --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('What are Globals?') }}</flux:heading>
        <flux:text class="mb-3">
            {{ __('Globals are named sets of variables that live outside of any specific page or entry. They are designed for site-wide data you need to reuse in multiple places — things like your company name, contact details, social media links, or a site-wide announcement banner.') }}
        </flux:text>
        <flux:text class="mb-3">
            {{ __('Unlike entries, globals are not tied to a URL or a collection. There is no concept of publishing or drafts — a global set and its values are simply stored and made available to your templates.') }}
        </flux:text>
        <flux:text>
            {{ __('Each global set can optionally be connected to a Blueprint, which defines the exact fields available in that set (text fields, rich text, images, etc.). Without a Blueprint, you can still store freeform key-value variables.') }}
        </flux:text>
    </div>

    {{-- How to Access --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('How to Access') }}</flux:heading>
        <flux:text class="mb-3">{{ __('Globals are managed from two areas of the CMS:') }}</flux:text>
        <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
            <li class="flex items-start gap-2">
                <flux:icon.arrow-right class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span>
                    <strong>{{ __('Sidebar') }}</strong> — {{ __('Navigate to') }}
                    <flux:badge size="sm" color="zinc">{{ __('Administration → Globals') }}</flux:badge>
                    {{ __('to see all global sets.') }}
                </span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.arrow-right class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span>
                    <strong>{{ __('Edit page') }}</strong> — {{ __('Click the edit button on any global set to open its field editor. If a Blueprint is assigned, the Blueprint\'s fields are rendered. Otherwise, freeform variable inputs are shown.') }}
                </span>
            </li>
        </ul>
    </div>

    {{-- Current Features --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('Current Features') }}</flux:heading>

        <div class="space-y-6">

            {{-- Global Sets --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Global Sets') }}</flux:heading>
                <flux:text class="mb-3">
                    {{ __('A Global Set is the top-level container for a group of related variables. Each set has:') }}
                </flux:text>
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
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('A human-readable label shown in the CMS, e.g. "Site Settings".') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Handle') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('A unique, slug-style identifier used to retrieve this set in code or templates, e.g. "site-settings". Auto-generated from the name but editable.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Blueprint') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('Optional. When assigned, the Blueprint defines the specific fields (and their types) that belong to this set.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Blueprint-driven fields --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Blueprint-Driven Fields') }}</flux:heading>
                <flux:text class="mb-3">
                    {{ __('When a Blueprint is assigned to a global set, the edit page renders that Blueprint\'s fields — the same field types used in entries (text, rich text, image, page builder, etc.). This gives you full type safety and structured data for your variables.') }}
                </flux:text>
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Switching a Blueprint on an existing set updates the field rendering immediately. Previously saved variable values are preserved by handle — as long as the handle matches the Blueprint field handle, values carry over.') }}
                </flux:text>
            </div>

            {{-- Freeform variables --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Freeform Variables (No Blueprint)') }}</flux:heading>
                <flux:text>
                    {{ __('If no Blueprint is assigned, a global set can still hold variables. Each variable is stored by its handle and a freeform value. This is useful for simple text or flag values that do not need a defined schema.') }}
                </flux:text>
            </div>

            {{-- Variable storage --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('How Variables Are Stored') }}</flux:heading>
                <flux:text class="mb-3">
                    {{ __('Each variable within a global set is stored as a separate record identified by its handle. When you save a global set, each field value is upserted by handle — creating a new record if it does not exist, or updating the existing one.') }}
                </flux:text>
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Values are cast to arrays internally, so complex field types (like image fields or repeaters) are stored as structured JSON.') }}
                </flux:text>
            </div>

            {{-- Creating a Global Set --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Creating a Global Set') }}</flux:heading>
                <flux:text class="mb-3">{{ __('To create a new global set:') }}</flux:text>
                <ol class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300 list-none">
                    <li class="flex items-start gap-2">
                        <flux:badge size="sm" color="zinc">1</flux:badge>
                        <span>{{ __('Click "New Global" on the Globals index page.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:badge size="sm" color="zinc">2</flux:badge>
                        <span>{{ __('Enter a name. The handle is auto-generated but can be customized.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:badge size="sm" color="zinc">3</flux:badge>
                        <span>{{ __('Optionally select a Blueprint to define the set\'s fields.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:badge size="sm" color="zinc">4</flux:badge>
                        <span>{{ __('Click "Create". You will be redirected to the edit page to fill in the field values.') }}</span>
                    </li>
                </ol>
                <flux:text class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Only administrators can create new global sets. All authenticated users can view and edit existing set values.') }}
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
                <span><strong>{{ __('Frontend template helpers') }}</strong> — {{ __('A Blade directive or helper function to retrieve a global set\'s variables in views, e.g.') }} <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">global('site-settings')</code>.</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Revision history') }}</strong> — {{ __('Track changes to global variable values over time and restore previous versions.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Per-set access control') }}</strong> — {{ __('Restrict which roles can view or edit specific global sets.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Caching') }}</strong> — {{ __('Cache resolved global variable values to avoid repeated database reads on high-traffic pages.') }}</span>
            </li>
        </ul>
    </div>

</div>
