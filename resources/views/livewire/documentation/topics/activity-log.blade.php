<div class="space-y-8">

    {{-- Title --}}
    <div>
        <div class="flex items-center gap-3">
            <flux:icon.clock variant="outline" class="size-8 text-zinc-400" />
            <flux:heading size="xl">{{ __('Activity Log') }}</flux:heading>
        </div>
        <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
            {{ __('Automatic audit trail for all CMS content changes.') }}
        </flux:text>
    </div>

    <flux:separator />

    {{-- What is the Activity Log --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('What is the Activity Log?') }}</flux:heading>
        <flux:text class="mb-3">
            {{ __('The Activity Log is an automatic audit trail that records every content change made in the CMS. Every time a user creates, updates, or deletes content — entries, collections, blueprints, taxonomies, assets, or users — a record is stored with full context: who made the change, what was changed, and when.') }}
        </flux:text>
        <flux:text>
            {{ __('It is a read-only log intended for accountability and debugging. You cannot modify or delete individual records from the interface.') }}
        </flux:text>
    </div>

    {{-- How to Access --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('How to Access') }}</flux:heading>
        <flux:text class="mb-3">{{ __('The Activity Log is available in two places:') }}</flux:text>
        <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
            <li class="flex items-start gap-2">
                <flux:icon.arrow-right class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span>
                    <strong>{{ __('Sidebar') }}</strong> — {{ __('Navigate to') }}
                    <flux:badge size="sm" color="zinc">{{ __('Administration → Activity Log') }}</flux:badge>
                    {{ __('in the admin sidebar to see the full paginated log.') }}
                </span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.arrow-right class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span>
                    <strong>{{ __('Dashboard') }}</strong> — {{ __('The last 10 activity entries are shown in the "Recent Activity" widget on the Dashboard.') }}
                </span>
            </li>
        </ul>
    </div>

    {{-- Current Features --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('Current Features') }}</flux:heading>

        <div class="space-y-6">

            {{-- Table Columns --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Log Table') }}</flux:heading>
                <flux:text class="mb-3">{{ __('The Activity Log table displays the following columns for each record:') }}</flux:text>
                <div class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-800">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-zinc-700 dark:text-zinc-300">{{ __('Column') }}</th>
                                <th class="px-4 py-2 text-left font-medium text-zinc-700 dark:text-zinc-300">{{ __('Description') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Event') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('The type of action taken. Color-coded:') }} <flux:badge size="sm" color="green">{{ __('created') }}</flux:badge> <flux:badge size="sm" color="blue">{{ __('updated') }}</flux:badge> <flux:badge size="sm" color="red">{{ __('deleted') }}</flux:badge></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Description') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('A human-readable summary of the action, e.g. "created entry".') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('User') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('The user who performed the action. Shown as "System" when triggered programmatically without a logged-in user.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Subject') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('The model type and ID that was affected, e.g. "Entry #12".') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Date') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('Relative time of the action (e.g. "2 hours ago"). Hover to see the full timestamp.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Changes') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('A "View" button appears when before/after data was recorded. Only available for update events that track property changes.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Filtering --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Filtering & Search') }}</flux:heading>
                <flux:text class="mb-3">{{ __('Three filter controls appear above the table:') }}</flux:text>
                <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <li class="flex items-start gap-2">
                        <flux:icon.magnifying-glass class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('Search') }}</strong> — {{ __('Full-text search on the activity description field. Results update as you type (300ms debounce).') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.funnel class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('Event filter') }}</strong> — {{ __('Filter by event type: All Events, Created, Updated, or Deleted.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.user class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('User filter') }}</strong> — {{ __('Filter to show activity by a specific user only.') }}</span>
                    </li>
                </ul>
                <flux:text class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Active filters are persisted in the URL query string') }}
                    (<code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">?q=</code>,
                    <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">?event=</code>,
                    <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">?user=</code>)
                    {{ __('so you can bookmark or share a filtered view.') }}
                </flux:text>
            </div>

            {{-- Change Diff Modal --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Change Details Modal') }}</flux:heading>
                <flux:text class="mb-3">
                    {{ __('For update events that captured property changes, a "View" button appears in the Changes column. Clicking it opens a modal showing a side-by-side diff:') }}
                </flux:text>
                <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <li class="flex items-start gap-2">
                        <flux:badge size="sm" color="red">{{ __('Before') }}</flux:badge>
                        <span class="mt-0.5">{{ __('The field values before the update was applied.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:badge size="sm" color="green">{{ __('After') }}</flux:badge>
                        <span class="mt-0.5">{{ __('The field values after the update was applied.') }}</span>
                    </li>
                </ul>
                <flux:text class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Array values (such as JSON fields) are displayed as JSON strings in the diff.') }}
                </flux:text>
            </div>

            {{-- Pagination --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Paginated Results') }}</flux:heading>
                <flux:text>
                    {{ __('The log is paginated at 20 records per page, ordered from most recent to oldest. Pagination controls appear at the bottom of the table when the result count exceeds one page.') }}
                </flux:text>
            </div>

        </div>
    </div>

    {{-- Future Features --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('Planned Future Features') }}</flux:heading>
        <flux:text class="mb-4">{{ __('The following features are planned for future releases:') }}</flux:text>
        <ul class="space-y-3 text-sm text-zinc-700 dark:text-zinc-300">
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Date range filtering') }}</strong> — {{ __('Filter the log to a specific date or time range.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('CSV export') }}</strong> — {{ __('Download the current filtered view as a CSV file for external reporting.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Per-model activity panel') }}</strong> — {{ __('View the activity history for a specific entry, collection, or asset directly from its edit page.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Retention policy') }}</strong> — {{ __('Configurable automatic purging of old activity records to keep the log manageable.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Event notifications') }}</strong> — {{ __('Email or in-app alerts triggered by specific activity events (e.g. notify on delete).') }}</span>
            </li>
        </ul>
    </div>

</div>
