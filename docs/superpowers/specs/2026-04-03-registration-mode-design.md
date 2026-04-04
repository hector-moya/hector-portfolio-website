# Registration Mode Control — Design Spec

**Date:** 2026-04-03  
**Status:** Approved

## Overview

The CMS supports four registration modes, configurable by an admin via a new Settings tab. The default is `closed`. Each mode controls who can create an account and under what conditions.

| Mode | Key | Behaviour |
|---|---|---|
| Closed | `closed` | `/register` is blocked entirely |
| Invitation only | `invitation` | Only users with a valid admin-sent invite link can register |
| Approval required | `approval` | Anyone can register, but must wait for admin approval after email verification |
| Open | `open` | Anyone can register immediately (current behaviour) |

---

## Section 1 — Data Layer

### `site_settings` table

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `key` | string, unique | e.g. `registration_mode` |
| `value` | text, nullable | |
| `created_at` / `updated_at` | timestamps | |

- A `SiteSetting` model with a static helper: `SiteSetting::get('registration_mode', 'closed')` and `SiteSetting::set('registration_mode', 'invitation')`.
- Seeded with `registration_mode = closed`.

### `invitations` table

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `email` | string | Recipient email |
| `role` | string | Default `viewer` |
| `token` | string, unique | 64-char random string |
| `invited_by` | FK → users | Admin who sent the invite |
| `expires_at` | timestamp | 48 hours from creation |
| `accepted_at` | timestamp, nullable | Null = not yet used |
| `created_at` / `updated_at` | timestamps | |

### `users` table — new `status` column

- `status` enum: `active` | `pending`, default `active`
- All existing users remain `active`.
- In Mode 3, newly registered users are set to `pending` until an admin approves them.

---

## Section 2 — Registration Mode Enforcement

### Middleware: `EnsureRegistrationOpen`

Applied to the `register` route. Logic:

- `closed` → abort 403 with a "Registration is currently closed" message, or redirect to login with a flash message.
- `invitation` → allow only if the request has a `token` query parameter matching a valid, unexpired, unused invitation. Otherwise abort 403.
- `approval` → allow freely (status is set to `pending` post-registration).
- `open` → allow freely.

### `Register` Livewire component changes

- Accepts an optional `token` string property (from query param).
- In invitation mode: validates the token on mount, pre-fills the email field (read-only), sets `invited_by` reference.
- On successful registration in invitation mode: marks `invitations.accepted_at = now()`.
- On successful registration in approval mode: sets `users.status = pending`, does not log the user in, redirects to `/pending-approval`.

---

## Section 3 — Settings UI

### New route: `settings/security`

- Livewire component: `App\Livewire\Settings\Security`
- Protected by `$this->authorize('viewAny', User::class)` — admin only.
- Added to the settings sidebar nav, visible to admins only.

### Registration Mode selector

A radio group with four options, each with a label and short description. Saving calls `SiteSetting::set('registration_mode', $value)`.

### Invite User form (visible when mode is `invitation`)

- Fields: email, role (select: viewer / editor / admin).
- On submit: creates an `Invitation` record, dispatches `SendInvitationEmail` mailable.
- Below the form: a table of sent invitations showing email, role, sent at, and status (Pending / Accepted / Expired).
- Expired invitations: `accepted_at` is null and `expires_at` is in the past.

### Invitation email

- Contains the registration link: `/register?token={token}`
- Subject: "You've been invited to {APP_NAME}"
- Sent via the app's configured mailer (Amazon SES).

---

## Section 4 — Approval Flow (Mode 3)

### Registration

- User registers normally and verifies their email.
- After email verification, `status` is `pending`; user is redirected to `/pending-approval` instead of the dashboard.
- `AdminPendingUserNotification` mailable is dispatched to all admin users immediately after registration (triggered after `event(new Registered(...))`).

### `/pending-approval` page

- Auth-required route, but **excluded from `EnsureUserIsActive`** to prevent a redirect loop.
- Read-only page: "Your account is under review. You'll receive an email once approved."
- Accessible even without a verified email (in case they return to the page before verifying).

### Users index — admin approval

- Pending users display a `Pending` badge on their row.
- An **Approve** button appears on each pending user's row.
- `Users\Index` `approve(int $userId)` action calls `$this->authorize('approve', $user)`.
- On approval: `status → active`, dispatches `UserApproved` mailable to the user.

### `UserPolicy::approve`

```php
public function approve(User $user, User $model): bool
{
    return $user->isAdmin();
}
```

---

## Section 5 — Login Protection for Pending Users

### Middleware: `EnsureUserIsActive`

- Added to the `auth` middleware group in `bootstrap/app.php`.
- On every authenticated request: if `Auth::user()->status === 'pending'`, log the user out and redirect to `/pending-approval` with a flash message.
- Does not affect the `/pending-approval` route itself (excluded or checked before redirect loop).
- Active users are never affected regardless of current registration mode.

---

## Mailables

| Mailable | Recipient | Trigger |
|---|---|---|
| `SendInvitationEmail` | Invited email address | Admin sends invite in Settings > Security |
| `AdminPendingUserNotification` | All admin users | New user registers in approval mode |
| `UserApproved` | Approved user | Admin approves the user in Users index |

---

## Files to Create / Modify

### New files
- `database/migrations/..._create_site_settings_table.php`
- `database/migrations/..._create_invitations_table.php`
- `database/migrations/..._add_status_to_users_table.php`
- `app/Models/SiteSetting.php`
- `app/Models/Invitation.php`
- `app/Http/Middleware/EnsureRegistrationOpen.php`
- `app/Http/Middleware/EnsureUserIsActive.php`
- `app/Livewire/Settings/Security.php`
- `resources/views/livewire/settings/security.blade.php`
- `app/Livewire/Auth/PendingApproval.php`
- `resources/views/livewire/auth/pending-approval.blade.php`
- `app/Mail/SendInvitationEmail.php`
- `app/Mail/AdminPendingUserNotification.php`
- `app/Mail/UserApproved.php`
- `database/seeders/SiteSettingsSeeder.php`
- `tests/Feature/RegistrationModeTest.php`
- `tests/Feature/InvitationTest.php`
- `tests/Feature/UserApprovalTest.php`

### Modified files
- `app/Livewire/Auth/Register.php` — token support, pending status, invitation acceptance
- `app/Livewire/Users/Index.php` — approve action
- `app/Policies/UserPolicy.php` — `approve` method
- `routes/auth.php` — middleware on `register` route, add `/pending-approval` route
- `bootstrap/app.php` — register `EnsureUserIsActive` in auth middleware group
- `resources/views/components/layouts/settings/sidebar.blade.php` (or equivalent) — add Security tab for admins
