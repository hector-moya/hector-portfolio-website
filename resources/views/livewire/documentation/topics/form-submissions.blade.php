<div class="space-y-8">

    {{-- Title --}}
    <div>
        <div class="flex items-center gap-3">
            <flux:icon.envelope variant="outline" class="size-8 text-zinc-400" />
            <flux:heading size="xl">{{ __('Form Submissions') }}</flux:heading>
        </div>
        <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
            {{ __('View and manage contact form submissions collected from your website.') }}
        </flux:text>
    </div>

    <flux:separator />

    {{-- What is it --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('What are Form Submissions?') }}</flux:heading>
        <flux:text class="mb-3">
            {{ __('Form Submissions is a section of the CMS that collects and stores messages sent through contact or inquiry forms on your website. Each time a visitor fills out and submits a form, a new submission record is stored and made available for review in the admin panel.') }}
        </flux:text>
        <flux:text>
            {{ __('Submissions are grouped by form, so if you have multiple forms (e.g. a contact form and a project inquiry form), their entries are kept separate and easy to browse.') }}
        </flux:text>
    </div>

    {{-- How to Access --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('How to Access') }}</flux:heading>
        <flux:text class="mb-3">{{ __('Form Submissions can be accessed from the admin sidebar:') }}</flux:text>
        <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
            <li class="flex items-start gap-2">
                <flux:icon.arrow-right class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span>
                    <strong>{{ __('Sidebar') }}</strong> — {{ __('Navigate to') }}
                    <flux:badge size="sm" color="zinc">{{ __('Form Submissions') }}</flux:badge>
                    {{ __('in the admin sidebar. Select a specific form to view its submissions.') }}
                </span>
            </li>
        </ul>
    </div>

    {{-- Current Features --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('Current Features') }}</flux:heading>

        <div class="space-y-6">

            {{-- Submissions Table --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Submissions Table') }}</flux:heading>
                <flux:text class="mb-3">{{ __('Each form\'s submissions are displayed in a table with the following columns:') }}</flux:text>
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
                                <td class="px-4 py-2 font-medium">{{ __('Submitter') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('The name and/or email address of the person who submitted the form, if those fields were collected.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Date') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('Relative time of submission (e.g. "3 hours ago"). Hover to see the full timestamp.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Data') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('A summary of the submitted field values. Click a row to view the full submission.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Read / Unread --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Read / Unread State') }}</flux:heading>
                <flux:text class="mb-3">
                    {{ __('Submissions track a read/unread state so you can easily identify which entries have not yet been reviewed. Unread submissions are visually highlighted in the table.') }}
                </flux:text>
                <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <li class="flex items-start gap-2">
                        <flux:icon.envelope class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('Unread') }}</strong> — {{ __('New submissions that have not been opened yet appear with a highlighted indicator.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.envelope-open class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('Read') }}</strong> — {{ __('Opening a submission automatically marks it as read.') }}</span>
                    </li>
                </ul>
            </div>

            {{-- Deleting --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Deleting Submissions') }}</flux:heading>
                <flux:text>
                    {{ __('Individual submissions can be deleted from the table. Deletion is permanent — there is no trash or recovery mechanism. Use this to remove spam or test submissions.') }}
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
                <span><strong>{{ __('Email notifications') }}</strong> — {{ __('Automatically send an email alert when a new submission is received.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('CSV export') }}</strong> — {{ __('Export all or filtered submissions for a form as a CSV file.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Spam filtering') }}</strong> — {{ __('Integrate with honeypot or CAPTCHA solutions to reduce spam submissions.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Submission reply') }}</strong> — {{ __('Reply to a submission directly from the CMS without leaving the admin panel.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Retention policy') }}</strong> — {{ __('Automatically purge submissions older than a configurable number of days.') }}</span>
            </li>
        </ul>
    </div>

</div>
