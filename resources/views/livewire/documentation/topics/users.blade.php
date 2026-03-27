<div class="space-y-8">

    {{-- Title --}}
    <div>
        <div class="flex items-center gap-3">
            <flux:icon.user-group variant="outline" class="size-8 text-zinc-400" />
            <flux:heading size="xl">{{ __('Users') }}</flux:heading>
        </div>
        <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
            {{ __('Manage who has access to the CMS and what they are allowed to do.') }}
        </flux:text>
    </div>

    <flux:separator />

    {{-- What is it --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('What is User Management?') }}</flux:heading>
        <flux:text class="mb-3">
            {{ __('User Management allows administrators to control who can log in to the CMS and what sections they can access. Each user has a role that determines their permissions across the application.') }}
        </flux:text>
        <flux:text>
            {{ __('Access to user management is restricted by policy — only users with the appropriate role can view the user list, create new users, or modify existing accounts.') }}
        </flux:text>
    </div>

    {{-- How to Access --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('How to Access') }}</flux:heading>
        <flux:text class="mb-3">{{ __('User Management is available from the admin sidebar:') }}</flux:text>
        <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
            <li class="flex items-start gap-2">
                <flux:icon.arrow-right class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span>
                    <strong>{{ __('Sidebar') }}</strong> — {{ __('Navigate to') }}
                    <flux:badge size="sm" color="zinc">{{ __('Administration → Users') }}</flux:badge>
                    {{ __('in the admin sidebar to view and manage all users.') }}
                </span>
            </li>
        </ul>
        <flux:text class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
            {{ __('This section is only visible to users whose role grants the') }}
            <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">viewAny</code>
            {{ __('permission on the User policy.') }}
        </flux:text>
    </div>

    {{-- Current Features --}}
    <div>
        <flux:heading size="lg" class="mb-3">{{ __('Current Features') }}</flux:heading>

        <div class="space-y-6">

            {{-- User List --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('User List') }}</flux:heading>
                <flux:text class="mb-3">{{ __('The Users page displays all registered accounts in a table with the following columns:') }}</flux:text>
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
                                <td class="px-4 py-2 font-medium">{{ __('Name') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('The user\'s display name and avatar (if set).') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Email') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('The user\'s email address used to log in.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Role') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('The role assigned to the user, displayed as a badge.') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-medium">{{ __('Actions') }}</td>
                                <td class="px-4 py-2 text-zinc-600 dark:text-zinc-400">{{ __('Edit or delete the user account. Destructive actions require confirmation.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Creating / Inviting Users --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Creating & Inviting Users') }}</flux:heading>
                <flux:text class="mb-3">
                    {{ __('New users can be created directly from the Users page. Fill in the name, email address, and assign a role. The user will receive an invitation email with a link to set their password.') }}
                </flux:text>
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('Only users with permission to create accounts will see the "New User" button.') }}
                </flux:text>
            </div>

            {{-- Editing Profiles --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Editing a User Profile') }}</flux:heading>
                <flux:text class="mb-3">
                    {{ __('Clicking the edit action for a user opens their profile form. The following fields can be updated:') }}
                </flux:text>
                <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                    <li class="flex items-start gap-2">
                        <flux:icon.pencil class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('Name') }}</strong> — {{ __('The user\'s display name shown throughout the CMS.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.pencil class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('Email') }}</strong> — {{ __('The login email address.') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <flux:icon.pencil class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                        <span><strong>{{ __('Role') }}</strong> — {{ __('The user\'s assigned role, which controls their CMS permissions.') }}</span>
                    </li>
                </ul>
            </div>

            {{-- Roles & Access Control --}}
            <div>
                <flux:heading size="sm" class="mb-2">{{ __('Role-Based Access Control') }}</flux:heading>
                <flux:text class="mb-3">
                    {{ __('Every user is assigned a role that determines what they can see and do in the CMS. Permissions are enforced via Laravel policy gates — each action (view, create, update, delete) is checked against the authenticated user\'s role before the action is allowed.') }}
                </flux:text>
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                    {{ __('The') }}
                    <code class="rounded bg-zinc-100 px-1 py-0.5 text-xs dark:bg-zinc-700">viewAny</code>
                    {{ __('gate on the User policy controls access to the Users section itself. If a user does not have this permission, the section will not appear in their sidebar.') }}
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
                <span><strong>{{ __('Custom roles') }}</strong> — {{ __('Create and configure roles with granular permission sets instead of predefined roles.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Two-factor authentication') }}</strong> — {{ __('Require or allow users to enable 2FA for added account security.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Last login tracking') }}</strong> — {{ __('Display the last time each user logged in to help identify inactive accounts.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Suspend / deactivate accounts') }}</strong> — {{ __('Temporarily block a user\'s access without deleting their account.') }}</span>
            </li>
            <li class="flex items-start gap-2">
                <flux:icon.clock variant="outline" class="mt-0.5 size-4 shrink-0 text-zinc-400" />
                <span><strong>{{ __('Audit trail per user') }}</strong> — {{ __('View a filtered activity log scoped to a specific user directly from their profile.') }}</span>
            </li>
        </ul>
    </div>

</div>
