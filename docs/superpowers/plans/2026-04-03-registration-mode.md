# Registration Mode Control Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add four registration modes (closed, invitation, approval, open) controlled by an admin settings toggle, with invitation emails and a manual approval workflow.

**Architecture:** A `site_settings` key-value table stores the active mode; middleware enforces it on the register route and protects active-only routes; the existing Livewire Register component is extended with token and approval support; a new Settings > Security tab provides the admin UI.

**Tech Stack:** Laravel 12, Livewire 4, Flux UI v2, Fortify, Pest 4, Amazon SES (via Laravel Mail)

---

## File Map

| Path | Action | Purpose |
|---|---|---|
| `database/migrations/..._create_site_settings_table.php` | Create | `site_settings` key-value table |
| `database/migrations/..._create_invitations_table.php` | Create | `invitations` table |
| `database/migrations/..._add_status_to_users_table.php` | Create | `status` column on `users` |
| `app/Models/SiteSetting.php` | Create | Key-value model with `get`/`set` helpers |
| `app/Models/Invitation.php` | Create | Invitation model with state helpers |
| `database/factories/InvitationFactory.php` | Create | Factory with `expired`/`accepted` states |
| `database/seeders/SiteSettingsSeeder.php` | Create | Seeds `registration_mode = closed` |
| `database/seeders/DatabaseSeeder.php` | Modify | Call `SiteSettingsSeeder` |
| `app/Http/Middleware/EnsureRegistrationOpen.php` | Create | Enforces mode on register route |
| `app/Http/Middleware/EnsureUserIsActive.php` | Create | Blocks pending users from all routes except pending-approval |
| `bootstrap/app.php` | Modify | Register `EnsureUserIsActive` in web middleware |
| `routes/auth.php` | Modify | Add `EnsureRegistrationOpen` to register route, add pending-approval route |
| `app/Livewire/Auth/Register.php` | Modify | Token support, pending status, invitation acceptance |
| `resources/views/livewire/auth/register.blade.php` | Modify | Read-only email field when token is present |
| `app/Livewire/Auth/PendingApproval.php` | Create | Waiting page component |
| `resources/views/livewire/auth/pending-approval.blade.php` | Create | "Your account is under review" page |
| `app/Livewire/Settings/Security.php` | Create | Registration mode toggle + invite form |
| `resources/views/livewire/settings/security.blade.php` | Create | Settings > Security view |
| `resources/views/components/settings/layout.blade.php` | Modify | Add Security tab (admin-only) |
| `routes/web.php` | Modify | Add `settings/security` route |
| `app/Livewire/Actions/Users/InviteUser.php` | Create | Creates invitation + dispatches email |
| `app/Mail/InvitationMail.php` | Create | Invitation email with magic link |
| `resources/views/emails/invitation.blade.php` | Create | Invitation email template |
| `app/Mail/AdminPendingUserNotification.php` | Create | Notifies all admins of pending registration |
| `resources/views/emails/admin-pending-user.blade.php` | Create | Admin notification email template |
| `app/Mail/UserApproved.php` | Create | Notifies user their account is approved |
| `resources/views/emails/user-approved.blade.php` | Create | Approval email template |
| `app/Policies/UserPolicy.php` | Modify | Add `approve` method |
| `app/Livewire/Users/Index.php` | Modify | Add `approve` action |
| `resources/views/livewire/users/index.blade.php` | Modify | Pending badge + Approve button |
| `tests/Feature/Auth/RegistrationModeTest.php` | Create | Tests for all 4 modes + middleware |
| `tests/Feature/Auth/InvitationTest.php` | Create | Invitation sending, token validation, registration |
| `tests/Feature/Auth/ApprovalTest.php` | Create | Approval flow end-to-end |
| `tests/Feature/Users/UserApprovalTest.php` | Create | Admin approve action + policy |

---

## Task 1: Data Layer — Migrations and Models

**Files:**
- Create: `database/migrations/2026_04_03_000001_create_site_settings_table.php`
- Create: `database/migrations/2026_04_03_000002_create_invitations_table.php`
- Create: `database/migrations/2026_04_03_000003_add_status_to_users_table.php`
- Create: `app/Models/SiteSetting.php`
- Create: `app/Models/Invitation.php`
- Create: `database/factories/InvitationFactory.php`

- [ ] **Step 1: Create the site_settings migration**

Run: `php artisan make:migration create_site_settings_table --no-interaction`

Then open the generated file and replace its `up`/`down` methods with:

```php
public function up(): void
{
    Schema::create('site_settings', function (Blueprint $table): void {
        $table->id();
        $table->string('key')->unique();
        $table->text('value')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('site_settings');
}
```

- [ ] **Step 2: Create the invitations migration**

Run: `php artisan make:migration create_invitations_table --no-interaction`

Replace its `up`/`down` methods:

```php
public function up(): void
{
    Schema::create('invitations', function (Blueprint $table): void {
        $table->id();
        $table->string('email');
        $table->string('role')->default('viewer');
        $table->string('token', 64)->unique();
        $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
        $table->timestamp('expires_at');
        $table->timestamp('accepted_at')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('invitations');
}
```

- [ ] **Step 3: Create the add_status_to_users migration**

Run: `php artisan make:migration add_status_to_users_table --no-interaction`

Replace its `up`/`down` methods:

```php
public function up(): void
{
    Schema::table('users', function (Blueprint $table): void {
        $table->string('status')->default('active')->after('role');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table): void {
        $table->dropColumn('status');
    });
}
```

- [ ] **Step 4: Run the migrations**

Run: `php artisan migrate --no-interaction`

Expected: All three migrations run successfully.

- [ ] **Step 5: Create the SiteSetting model**

Run: `php artisan make:model SiteSetting --no-interaction`

Replace `app/Models/SiteSetting.php`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
```

- [ ] **Step 6: Create the Invitation model**

Run: `php artisan make:model Invitation --no-interaction`

Replace `app/Models/Invitation.php`:

```php
<?php

namespace App\Models;

use Database\Factories\InvitationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $email
 * @property string $role
 * @property string $token
 * @property int $invited_by
 * @property Carbon $expires_at
 * @property Carbon|null $accepted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Invitation extends Model
{
    /** @use HasFactory<InvitationFactory> */
    use HasFactory;

    protected $fillable = ['email', 'role', 'token', 'invited_by', 'expires_at', 'accepted_at'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function isPending(): bool
    {
        return $this->accepted_at === null && $this->expires_at->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->accepted_at === null && $this->expires_at->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }
}
```

- [ ] **Step 7: Create the InvitationFactory**

Run: `php artisan make:factory InvitationFactory --no-interaction`

Replace `database/factories/InvitationFactory.php`:

```php
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'role' => 'viewer',
            'token' => Str::random(64),
            'invited_by' => User::factory()->admin(),
            'expires_at' => now()->addHours(48),
            'accepted_at' => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes): array => [
            'expires_at' => now()->subHour(),
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes): array => [
            'accepted_at' => now()->subHour(),
        ]);
    }
}
```

- [ ] **Step 8: Update UserFactory to include status**

In `database/factories/UserFactory.php`, add `'status' => 'active'` to the `definition()` array and add a `pending()` state:

```php
// In definition():
'status' => 'active',

// New state method:
public function pending(): static
{
    return $this->state(fn (array $attributes): array => [
        'status' => 'pending',
    ]);
}
```

- [ ] **Step 9: Commit**

```bash
git add database/migrations app/Models/SiteSetting.php app/Models/Invitation.php database/factories/InvitationFactory.php database/factories/UserFactory.php
git commit -m "feat: add site_settings, invitations tables and models"
```

---

## Task 2: Seeder

**Files:**
- Create: `database/seeders/SiteSettingsSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`

- [ ] **Step 1: Create the seeder**

Run: `php artisan make:seeder SiteSettingsSeeder --no-interaction`

Replace `database/seeders/SiteSettingsSeeder.php`:

```php
<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        SiteSetting::updateOrCreate(
            ['key' => 'registration_mode'],
            ['value' => 'closed']
        );
    }
}
```

- [ ] **Step 2: Register the seeder**

Open `database/seeders/DatabaseSeeder.php` and add `SiteSettingsSeeder` to the `run()` call:

```php
$this->call([
    SiteSettingsSeeder::class,
]);
```

- [ ] **Step 3: Run the seeder**

Run: `php artisan db:seed --class=SiteSettingsSeeder --no-interaction`

Expected: `site_settings` table has one row with `key=registration_mode`, `value=closed`.

- [ ] **Step 4: Commit**

```bash
git add database/seeders/SiteSettingsSeeder.php database/seeders/DatabaseSeeder.php
git commit -m "feat: seed default registration_mode as closed"
```

---

## Task 3: EnsureRegistrationOpen Middleware

**Files:**
- Create: `app/Http/Middleware/EnsureRegistrationOpen.php`
- Modify: `routes/auth.php`
- Create: `tests/Feature/Auth/RegistrationModeTest.php`

- [ ] **Step 1: Write failing tests**

Run: `php artisan make:test Feature/Auth/RegistrationModeTest --pest --no-interaction`

Replace `tests/Feature/Auth/RegistrationModeTest.php`:

```php
<?php

use App\Models\Invitation;
use App\Models\SiteSetting;
use App\Models\User;

beforeEach(function (): void {
    SiteSetting::set('registration_mode', 'closed');
});

test('register page is blocked when mode is closed', function (): void {
    $this->get(route('register'))
        ->assertRedirect(route('login'));
});

test('register page shows closed flash message', function (): void {
    $this->get(route('register'))
        ->assertSessionHas('status', 'Registration is currently closed.');
});

test('register page is accessible when mode is open', function (): void {
    SiteSetting::set('registration_mode', 'open');

    $this->get(route('register'))
        ->assertOk();
});

test('register page is accessible when mode is approval', function (): void {
    SiteSetting::set('registration_mode', 'approval');

    $this->get(route('register'))
        ->assertOk();
});

test('register page is blocked when mode is invitation and no token provided', function (): void {
    SiteSetting::set('registration_mode', 'invitation');

    $this->get(route('register'))
        ->assertRedirect(route('login'));
});

test('register page is accessible with a valid invitation token', function (): void {
    SiteSetting::set('registration_mode', 'invitation');

    $invitation = Invitation::factory()->create();

    $this->get(route('register', ['token' => $invitation->token]))
        ->assertOk();
});

test('register page is blocked with an expired invitation token', function (): void {
    SiteSetting::set('registration_mode', 'invitation');

    $invitation = Invitation::factory()->expired()->create();

    $this->get(route('register', ['token' => $invitation->token]))
        ->assertRedirect(route('login'));
});

test('register page is blocked with an already-accepted invitation token', function (): void {
    SiteSetting::set('registration_mode', 'invitation');

    $invitation = Invitation::factory()->accepted()->create();

    $this->get(route('register', ['token' => $invitation->token]))
        ->assertRedirect(route('login'));
});
```

- [ ] **Step 2: Run tests to confirm they fail**

Run: `php artisan test --compact --filter=RegistrationModeTest`

Expected: All 8 tests FAIL (middleware does not exist yet).

- [ ] **Step 3: Create the middleware**

Run: `php artisan make:middleware EnsureRegistrationOpen --no-interaction`

Replace `app/Http/Middleware/EnsureRegistrationOpen.php`:

```php
<?php

namespace App\Http\Middleware;

use App\Models\Invitation;
use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRegistrationOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        $mode = SiteSetting::get('registration_mode', 'closed');

        if ($mode === 'closed') {
            return redirect()->route('login')
                ->with('status', 'Registration is currently closed.');
        }

        if ($mode === 'invitation') {
            $token = $request->query('token');

            if (! $token) {
                return redirect()->route('login')
                    ->with('status', 'Registration is by invitation only.');
            }

            $invitation = Invitation::where('token', $token)
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->first();

            if (! $invitation) {
                return redirect()->route('login')
                    ->with('status', 'Invalid or expired invitation link.');
            }
        }

        return $next($request);
    }
}
```

- [ ] **Step 4: Apply the middleware to the register route**

Open `routes/auth.php` and update the register route:

```php
use App\Http\Middleware\EnsureRegistrationOpen;

Route::middleware('guest')->group(function (): void {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)
        ->middleware(EnsureRegistrationOpen::class)
        ->name('register');
    Route::get('forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
});
```

- [ ] **Step 5: Run tests to confirm they pass**

Run: `php artisan test --compact --filter=RegistrationModeTest`

Expected: All 8 tests PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Http/Middleware/EnsureRegistrationOpen.php routes/auth.php tests/Feature/Auth/RegistrationModeTest.php
git commit -m "feat: add EnsureRegistrationOpen middleware for registration mode control"
```

---

## Task 4: EnsureUserIsActive Middleware

**Files:**
- Create: `app/Http/Middleware/EnsureUserIsActive.php`
- Modify: `bootstrap/app.php`
- Modify: `routes/auth.php` (add pending-approval route)
- Create: `app/Livewire/Auth/PendingApproval.php`
- Create: `resources/views/livewire/auth/pending-approval.blade.php`

- [ ] **Step 1: Write failing tests**

Run: `php artisan make:test Feature/Auth/ApprovalTest --pest --no-interaction`

Replace `tests/Feature/Auth/ApprovalTest.php`:

```php
<?php

use App\Models\User;

test('pending user is redirected to pending-approval on any authenticated route', function (): void {
    $pending = User::factory()->pending()->create();

    $this->actingAs($pending)
        ->get(route('dashboard'))
        ->assertRedirect(route('pending-approval'));
});

test('active user is not redirected to pending-approval', function (): void {
    $active = User::factory()->admin()->create();

    $this->actingAs($active)
        ->get(route('dashboard'))
        ->assertOk();
});

test('pending user can access the pending-approval page', function (): void {
    $pending = User::factory()->pending()->create();

    $this->actingAs($pending)
        ->get(route('pending-approval'))
        ->assertOk();
});

test('unauthenticated user cannot access pending-approval page', function (): void {
    $this->get(route('pending-approval'))
        ->assertRedirect(route('login'));
});
```

- [ ] **Step 2: Run tests to confirm they fail**

Run: `php artisan test --compact --filter=ApprovalTest`

Expected: All 4 tests FAIL (middleware and route do not exist yet).

- [ ] **Step 3: Create the PendingApproval Livewire component**

Run: `php artisan make:livewire Auth/PendingApproval --no-interaction`

Replace `app/Livewire/Auth/PendingApproval.php`:

```php
<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class PendingApproval extends Component
{
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.auth.pending-approval');
    }
}
```

Replace `resources/views/livewire/auth/pending-approval.blade.php`:

```blade
<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Account Pending Approval')"
        :description="__('Your account is under review. You\'ll receive an email once an administrator approves your registration.')"
    />

    <flux:card class="text-center">
        <flux:icon.clock class="mx-auto mb-4 size-12 text-yellow-500" />
        <flux:heading>{{ __('Hang tight!') }}</flux:heading>
        <flux:subheading class="mt-2">
            {{ __('Our team will review your account and notify you by email when you\'re approved.') }}
        </flux:subheading>
    </flux:card>

    <div class="text-center text-sm">
        <flux:link :href="route('logout')" wire:navigate>{{ __('Back to login') }}</flux:link>
    </div>
</div>
```

- [ ] **Step 4: Add the pending-approval route**

Open `routes/auth.php` and add inside the `auth` middleware group:

```php
Route::middleware('auth')->group(function (): void {
    Route::get('verify-email', VerifyEmail::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('pending-approval', \App\Livewire\Auth\PendingApproval::class)
        ->name('pending-approval');
});
```

- [ ] **Step 5: Create the EnsureUserIsActive middleware**

Run: `php artisan make:middleware EnsureUserIsActive --no-interaction`

Replace `app/Http/Middleware/EnsureUserIsActive.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->status === 'pending') {
            if (! $request->routeIs(['pending-approval', 'verification.notice', 'verification.verify', 'logout'])) {
                return redirect()->route('pending-approval');
            }
        }

        return $next($request);
    }
}
```

- [ ] **Step 6: Register the middleware in bootstrap/app.php**

Open `bootstrap/app.php` and update `withMiddleware`:

```php
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\SetLocale;

->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(SetLocale::class);
    $middleware->web(EnsureUserIsActive::class);
})
```

- [ ] **Step 7: Run tests to confirm they pass**

Run: `php artisan test --compact --filter=ApprovalTest`

Expected: All 4 tests PASS.

- [ ] **Step 8: Confirm existing tests still pass**

Run: `php artisan test --compact --filter=RegistrationTest`

Expected: All existing registration tests PASS (open mode needed — note: existing tests run with no `site_settings` row so `get()` returns the `closed` default and they will fail).

Fix: Add `SiteSetting::set('registration_mode', 'open');` to `tests/Feature/Auth/RegistrationTest.php` — open the file and add a `beforeEach`:

```php
use App\Models\SiteSetting;

beforeEach(function (): void {
    SiteSetting::set('registration_mode', 'open');
});
```

Run again: `php artisan test --compact --filter=RegistrationTest`

Expected: All PASS.

- [ ] **Step 9: Commit**

```bash
git add app/Http/Middleware/EnsureUserIsActive.php bootstrap/app.php routes/auth.php app/Livewire/Auth/PendingApproval.php resources/views/livewire/auth/pending-approval.blade.php tests/Feature/Auth/ApprovalTest.php tests/Feature/Auth/RegistrationTest.php
git commit -m "feat: add EnsureUserIsActive middleware and pending-approval page"
```

---

## Task 5: Settings > Security Tab

**Files:**
- Create: `app/Livewire/Settings/Security.php`
- Create: `resources/views/livewire/settings/security.blade.php`
- Modify: `routes/web.php`
- Modify: `resources/views/components/settings/layout.blade.php`

- [ ] **Step 1: Create the Security Livewire component**

Run: `php artisan make:livewire Settings/Security --no-interaction`

Replace `app/Livewire/Settings/Security.php`:

```php
<?php

namespace App\Livewire\Settings;

use App\Models\SiteSetting;
use App\Models\User;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

class Security extends Component
{
    public string $registrationMode = 'closed';

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);

        $this->registrationMode = SiteSetting::get('registration_mode', 'closed');
    }

    public function saveMode(): void
    {
        $this->authorize('viewAny', User::class);

        $this->validate([
            'registrationMode' => ['required', 'in:closed,invitation,approval,open'],
        ]);

        SiteSetting::set('registration_mode', $this->registrationMode);

        Flux::toast(
            heading: 'Settings Saved',
            text: 'Registration mode has been updated.',
            variant: 'success',
        );
    }

    #[Title('Security')]
    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.settings.security');
    }
}
```

- [ ] **Step 2: Create the security view**

Create `resources/views/livewire/settings/security.blade.php`:

```blade
<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Security')" :subheading="__('Control who can register for an account')">
        <form wire:submit="saveMode" class="my-6 w-full space-y-6">
            <div class="space-y-4">
                <flux:heading size="sm">{{ __('Registration Mode') }}</flux:heading>
                <flux:radio.group wire:model="registrationMode" class="space-y-3">
                    <flux:radio
                        value="closed"
                        :label="__('Closed')"
                        :description="__('Registration is disabled. No one can create an account.')"
                    />
                    <flux:radio
                        value="invitation"
                        :label="__('Invitation Only')"
                        :description="__('Only users you invite via email can register.')"
                    />
                    <flux:radio
                        value="approval"
                        :label="__('Approval Required')"
                        :description="__('Anyone can register, but you must manually approve each account.')"
                    />
                    <flux:radio
                        value="open"
                        :label="__('Open')"
                        :description="__('Anyone can register immediately. Use with caution.')"
                    />
                </flux:radio.group>
            </div>

            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
        </form>
    </x-settings.layout>
</section>
```

- [ ] **Step 3: Add the route**

Open `routes/web.php` and add inside the `auth` middleware group alongside the other settings routes:

```php
Route::get('settings/security', \App\Livewire\Settings\Security::class)
    ->name('settings.security');
```

- [ ] **Step 4: Add the Security tab to the settings nav**

Open `resources/views/components/settings/layout.blade.php` and add the Security tab after Appearance, visible only to admins:

```blade
<flux:navlist>
    <flux:navlist.item :href="route('settings.profile')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
    <flux:navlist.item :href="route('settings.password')" wire:navigate>{{ __('Password') }}</flux:navlist.item>
    @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
        <flux:navlist.item :href="route('two-factor.show')" wire:navigate>{{ __('Two-Factor Auth') }}</flux:navlist.item>
    @endif
    <flux:navlist.item :href="route('settings.appearance')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
    @can('viewAny', App\Models\User::class)
        <flux:navlist.item :href="route('settings.security')" wire:navigate>{{ __('Security') }}</flux:navlist.item>
    @endcan
</flux:navlist>
```

- [ ] **Step 5: Verify admin can access the security settings page**

Run: `php artisan test --compact --filter=SecuritySettingsTest`

(No test file yet — create it inline.)

Run: `php artisan make:test Feature/Settings/SecuritySettingsTest --pest --no-interaction`

Replace `tests/Feature/Settings/SecuritySettingsTest.php`:

```php
<?php

use App\Livewire\Settings\Security;
use App\Models\SiteSetting;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    SiteSetting::set('registration_mode', 'open');
});

test('admin can access security settings', function (): void {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('settings.security'))
        ->assertOk();
});

test('non-admin cannot access security settings', function (): void {
    $editor = User::factory()->editor()->create();

    $this->actingAs($editor)
        ->get(route('settings.security'))
        ->assertForbidden();
});

test('admin can change registration mode', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Security::class)
        ->set('registrationMode', 'invitation')
        ->call('saveMode')
        ->assertHasNoErrors();

    expect(SiteSetting::get('registration_mode'))->toBe('invitation');
});

test('registration mode must be a valid value', function (): void {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Security::class)
        ->set('registrationMode', 'invalid-mode')
        ->call('saveMode')
        ->assertHasErrors(['registrationMode']);
});
```

Run: `php artisan test --compact --filter=SecuritySettingsTest`

Expected: All 4 tests PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Livewire/Settings/Security.php resources/views/livewire/settings/security.blade.php routes/web.php resources/views/components/settings/layout.blade.php tests/Feature/Settings/SecuritySettingsTest.php
git commit -m "feat: add Settings > Security tab with registration mode selector"
```

---

## Task 6: Invitation Mailable and Action

**Files:**
- Create: `app/Mail/InvitationMail.php`
- Create: `resources/views/emails/invitation.blade.php`
- Create: `app/Livewire/Actions/Users/InviteUser.php`
- Create: `tests/Feature/Auth/InvitationTest.php`

- [ ] **Step 1: Write failing tests**

Run: `php artisan make:test Feature/Auth/InvitationTest --pest --no-interaction`

Replace `tests/Feature/Auth/InvitationTest.php`:

```php
<?php

use App\Livewire\Actions\Users\InviteUser;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function (): void {
    SiteSetting::set('registration_mode', 'open');
});

test('admin can send an invitation', function (): void {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    app(InviteUser::class)->invite(
        email: 'invited@example.com',
        role: 'editor',
    );

    expect(Invitation::where('email', 'invited@example.com')->exists())->toBeTrue();
    Mail::assertSent(InvitationMail::class, fn ($mail) => $mail->hasTo('invited@example.com'));
});

test('invitation expires in 48 hours', function (): void {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    app(InviteUser::class)->invite(
        email: 'invited@example.com',
        role: 'viewer',
    );

    $invitation = Invitation::where('email', 'invited@example.com')->first();

    expect($invitation->expires_at)->toBeBetween(
        now()->addHours(47),
        now()->addHours(49)
    );
});

test('non-admin cannot send an invitation', function (): void {
    $editor = User::factory()->editor()->create();
    $this->actingAs($editor);

    expect(fn () => app(InviteUser::class)->invite(
        email: 'invited@example.com',
        role: 'viewer',
    ))->toThrow(\Illuminate\Auth\Access\AuthorizationException::class);
});

test('invitation email contains registration link with token', function (): void {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    app(InviteUser::class)->invite(
        email: 'invited@example.com',
        role: 'viewer',
    );

    Mail::assertSent(InvitationMail::class, function (InvitationMail $mail) {
        $invitation = Invitation::where('email', 'invited@example.com')->first();

        return $mail->invitation->token === $invitation->token;
    });
});
```

- [ ] **Step 2: Run tests to confirm they fail**

Run: `php artisan test --compact --filter=InvitationTest`

Expected: All 4 tests FAIL.

- [ ] **Step 3: Create the InvitationMail mailable**

Run: `php artisan make:mail InvitationMail --no-interaction`

Replace `app/Mail/InvitationMail.php`:

```php
<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Invitation $invitation) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'ve been invited to '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
        );
    }
}
```

- [ ] **Step 4: Create the invitation email view**

Create `resources/views/emails/invitation.blade.php`:

```blade
<x-mail::message>
# You've been invited to {{ config('app.name') }}

You have been invited to create an account as a **{{ $invitation->role }}**.

This invitation link expires in 48 hours.

<x-mail::button :url="route('register', ['token' => $invitation->token])">
Accept Invitation
</x-mail::button>

If you did not expect this invitation, you can safely ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
```

- [ ] **Step 5: Create the InviteUser action**

Create `app/Livewire/Actions/Users/InviteUser.php`:

```php
<?php

namespace App\Livewire\Actions\Users;

use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InviteUser
{
    public function invite(string $email, string $role): Invitation
    {
        Gate::authorize('create', User::class);

        $invitation = Invitation::create([
            'email' => $email,
            'role' => $role,
            'token' => Str::random(64),
            'invited_by' => auth()->id(),
            'expires_at' => now()->addHours(48),
        ]);

        Mail::to($email)->send(new InvitationMail($invitation));

        return $invitation;
    }
}
```

- [ ] **Step 6: Run tests to confirm they pass**

Run: `php artisan test --compact --filter=InvitationTest`

Expected: All 4 tests PASS.

- [ ] **Step 7: Commit**

```bash
git add app/Mail/InvitationMail.php resources/views/emails/invitation.blade.php app/Livewire/Actions/Users/InviteUser.php tests/Feature/Auth/InvitationTest.php
git commit -m "feat: add InvitationMail mailable and InviteUser action"
```

---

## Task 7: Invite Form in Security Settings

**Files:**
- Modify: `app/Livewire/Settings/Security.php`
- Modify: `resources/views/livewire/settings/security.blade.php`

- [ ] **Step 1: Add invite properties and method to the Security component**

Open `app/Livewire/Settings/Security.php` and add:

```php
<?php

namespace App\Livewire\Settings;

use App\Livewire\Actions\Users\InviteUser;
use App\Models\Invitation;
use App\Models\SiteSetting;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class Security extends Component
{
    public string $registrationMode = 'closed';

    public string $inviteEmail = '';

    public string $inviteRole = 'viewer';

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);

        $this->registrationMode = SiteSetting::get('registration_mode', 'closed');
    }

    public function saveMode(): void
    {
        $this->authorize('viewAny', User::class);

        $this->validate([
            'registrationMode' => ['required', 'in:closed,invitation,approval,open'],
        ]);

        SiteSetting::set('registration_mode', $this->registrationMode);

        Flux::toast(
            heading: 'Settings Saved',
            text: 'Registration mode has been updated.',
            variant: 'success',
        );
    }

    public function sendInvite(): void
    {
        $this->authorize('viewAny', User::class);

        $this->validate([
            'inviteEmail' => ['required', 'email', 'unique:invitations,email'],
            'inviteRole' => ['required', 'in:admin,editor,viewer'],
        ]);

        app(InviteUser::class)->invite(
            email: $this->inviteEmail,
            role: $this->inviteRole,
        );

        $this->reset('inviteEmail', 'inviteRole');
        $this->inviteRole = 'viewer';

        Flux::toast(
            heading: 'Invitation Sent',
            text: 'The invitation email has been sent.',
            variant: 'success',
        );
    }

    #[Computed]
    public function invitations(): Collection
    {
        return Invitation::with('invitedBy')
            ->orderByDesc('created_at')
            ->get();
    }

    #[Title('Security')]
    public function render(): View
    {
        return view('livewire.settings.security');
    }
}
```

- [ ] **Step 2: Update the security view with invite form and invitation list**

Replace `resources/views/livewire/settings/security.blade.php`:

```blade
<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Security')" :subheading="__('Control who can register for an account')">
        {{-- Registration Mode --}}
        <form wire:submit="saveMode" class="my-6 w-full space-y-6">
            <div class="space-y-4">
                <flux:heading size="sm">{{ __('Registration Mode') }}</flux:heading>
                <flux:radio.group wire:model="registrationMode" class="space-y-3">
                    <flux:radio
                        value="closed"
                        :label="__('Closed')"
                        :description="__('Registration is disabled. No one can create an account.')"
                    />
                    <flux:radio
                        value="invitation"
                        :label="__('Invitation Only')"
                        :description="__('Only users you invite via email can register.')"
                    />
                    <flux:radio
                        value="approval"
                        :label="__('Approval Required')"
                        :description="__('Anyone can register, but you must manually approve each account.')"
                    />
                    <flux:radio
                        value="open"
                        :label="__('Open')"
                        :description="__('Anyone can register immediately. Use with caution.')"
                    />
                </flux:radio.group>
            </div>

            <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
        </form>

        {{-- Invite User Form (invitation mode only) --}}
        @if ($registrationMode === 'invitation')
            <flux:separator class="my-8" />

            <div class="space-y-6">
                <div>
                    <flux:heading size="sm">{{ __('Invite a User') }}</flux:heading>
                    <flux:subheading>{{ __('Send a 48-hour invitation link to a specific email address.') }}</flux:subheading>
                </div>

                <form wire:submit="sendInvite" class="space-y-4">
                    <flux:input
                        wire:model="inviteEmail"
                        :label="__('Email Address')"
                        type="email"
                        :placeholder="__('colleague@example.com')"
                    />

                    <flux:select wire:model="inviteRole" :label="__('Role')">
                        <flux:select.option value="viewer">{{ __('Viewer') }}</flux:select.option>
                        <flux:select.option value="editor">{{ __('Editor') }}</flux:select.option>
                        <flux:select.option value="admin">{{ __('Admin') }}</flux:select.option>
                    </flux:select>

                    <flux:button variant="primary" type="submit" icon="envelope">
                        {{ __('Send Invitation') }}
                    </flux:button>
                </form>

                {{-- Sent Invitations --}}
                @if ($this->invitations->isNotEmpty())
                    <div>
                        <flux:heading size="sm" class="mb-3">{{ __('Sent Invitations') }}</flux:heading>
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>{{ __('Email') }}</flux:table.column>
                                <flux:table.column>{{ __('Role') }}</flux:table.column>
                                <flux:table.column>{{ __('Sent') }}</flux:table.column>
                                <flux:table.column>{{ __('Status') }}</flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach ($this->invitations as $invitation)
                                    <flux:table.row wire:key="invitation-{{ $invitation->id }}">
                                        <flux:table.cell>{{ $invitation->email }}</flux:table.cell>
                                        <flux:table.cell>
                                            <flux:badge color="{{ $invitation->role === 'admin' ? 'blue' : ($invitation->role === 'editor' ? 'indigo' : 'zinc') }}">
                                                {{ __($invitation->role) }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell>{{ $invitation->created_at->diffForHumans() }}</flux:table.cell>
                                        <flux:table.cell>
                                            @if ($invitation->isAccepted())
                                                <flux:badge color="green">{{ __('Accepted') }}</flux:badge>
                                            @elseif ($invitation->isExpired())
                                                <flux:badge color="red">{{ __('Expired') }}</flux:badge>
                                            @else
                                                <flux:badge color="yellow">{{ __('Pending') }}</flux:badge>
                                            @endif
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                @endif
            </div>
        @endif
    </x-settings.layout>
</section>
```

- [ ] **Step 3: Add invite tests to SecuritySettingsTest**

Open `tests/Feature/Settings/SecuritySettingsTest.php` and append:

```php
test('admin can send an invitation from security settings', function (): void {
    Mail::fake();

    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(Security::class)
        ->set('registrationMode', 'invitation')
        ->call('saveMode')
        ->set('inviteEmail', 'newperson@example.com')
        ->set('inviteRole', 'editor')
        ->call('sendInvite')
        ->assertHasNoErrors();

    expect(\App\Models\Invitation::where('email', 'newperson@example.com')->exists())->toBeTrue();
});

test('invite email must be unique in invitations table', function (): void {
    $admin = User::factory()->admin()->create();
    \App\Models\Invitation::factory()->create(['email' => 'existing@example.com']);

    Livewire::actingAs($admin)
        ->test(Security::class)
        ->set('inviteEmail', 'existing@example.com')
        ->set('inviteRole', 'viewer')
        ->call('sendInvite')
        ->assertHasErrors(['inviteEmail']);
});
```

Add the `Mail` and `Livewire` imports at the top of the file:

```php
use Illuminate\Support\Facades\Mail;
```

- [ ] **Step 4: Run tests**

Run: `php artisan test --compact --filter=SecuritySettingsTest`

Expected: All tests PASS.

- [ ] **Step 5: Commit**

```bash
git add app/Livewire/Settings/Security.php resources/views/livewire/settings/security.blade.php tests/Feature/Settings/SecuritySettingsTest.php
git commit -m "feat: add invite user form and invitation list to security settings"
```

---

## Task 8: Register Component — Invitation Token Support

**Files:**
- Modify: `app/Livewire/Auth/Register.php`
- Modify: `resources/views/livewire/auth/register.blade.php`

- [ ] **Step 1: Write failing tests**

Add to `tests/Feature/Auth/InvitationTest.php`:

```php
use App\Livewire\Auth\Register;

test('register component pre-fills email from valid invitation token', function (): void {
    SiteSetting::set('registration_mode', 'invitation');

    $invitation = Invitation::factory()->create(['email' => 'invited@example.com']);

    Livewire::test(Register::class, ['token' => $invitation->token])
        ->assertSet('email', 'invited@example.com');
});

test('registering with invitation token marks invitation as accepted', function (): void {
    Mail::fake();
    SiteSetting::set('registration_mode', 'invitation');

    $invitation = Invitation::factory()->create(['email' => 'invited@example.com']);

    Livewire::test(Register::class, ['token' => $invitation->token])
        ->set('name', 'Invited User')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors();

    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});

test('registering in approval mode sets user status to pending', function (): void {
    Mail::fake();
    SiteSetting::set('registration_mode', 'approval');

    Livewire::test(Register::class)
        ->set('name', 'Pending User')
        ->set('email', 'pending@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors();

    $user = User::where('email', 'pending@example.com')->first();
    expect($user->status)->toBe('pending');
});
```

Add `use Livewire\Livewire;` to the imports of `InvitationTest.php`.

- [ ] **Step 2: Run tests to confirm they fail**

Run: `php artisan test --compact --filter=InvitationTest`

Expected: New 3 tests FAIL.

- [ ] **Step 3: Update the Register component**

Replace `app/Livewire/Auth/Register.php`:

```php
<?php

namespace App\Livewire\Auth;

use App\Models\Invitation;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    #[Url]
    public string $token = '';

    public function mount(): void
    {
        if ($this->token) {
            $invitation = Invitation::where('token', $this->token)
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->first();

            if ($invitation) {
                $this->email = $invitation->email;
            }
        }
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $mode = SiteSetting::get('registration_mode', 'closed');

        if ($mode === 'approval') {
            $validated['status'] = 'pending';
        }

        $user = User::query()->create($validated);

        event(new Registered($user));

        if ($mode === 'invitation' && $this->token) {
            Invitation::where('token', $this->token)
                ->whereNull('accepted_at')
                ->update(['accepted_at' => now()]);
        }

        Auth::login($user);

        Session::regenerate();

        $this->redirect(route('verification.notice', absolute: false), navigate: true);
    }
}
```

- [ ] **Step 4: Make email read-only when token is present**

Open `resources/views/livewire/auth/register.blade.php`. Locate the email input field and update it to be read-only when a token is present. The exact markup depends on the existing view — read the file first, then add `:readonly="(bool) $token"` to the `flux:input` for email. Example:

```blade
<flux:input
    wire:model="email"
    :label="__('Email address')"
    type="email"
    required
    autocomplete="email"
    :readonly="(bool) $token"
    :placeholder="__('email@example.com')"
/>
```

- [ ] **Step 5: Run tests to confirm they pass**

Run: `php artisan test --compact --filter=InvitationTest`

Expected: All tests PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Livewire/Auth/Register.php resources/views/livewire/auth/register.blade.php tests/Feature/Auth/InvitationTest.php
git commit -m "feat: add invitation token support and approval pending status to Register component"
```

---

## Task 9: Admin Pending User Notification

**Files:**
- Create: `app/Mail/AdminPendingUserNotification.php`
- Create: `resources/views/emails/admin-pending-user.blade.php`
- Modify: `app/Livewire/Auth/Register.php`

- [ ] **Step 1: Create the mailable**

Run: `php artisan make:mail AdminPendingUserNotification --no-interaction`

Replace `app/Mail/AdminPendingUserNotification.php`:

```php
<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminPendingUserNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly User $pendingUser) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Registration Awaiting Approval — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-pending-user',
        );
    }
}
```

- [ ] **Step 2: Create the email view**

Create `resources/views/emails/admin-pending-user.blade.php`:

```blade
<x-mail::message>
# New Registration Awaiting Approval

**{{ $pendingUser->name }}** ({{ $pendingUser->email }}) has registered and is waiting for approval.

<x-mail::button :url="route('users.index')">
Review in Users
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
```

- [ ] **Step 3: Dispatch notification from Register component**

Open `app/Livewire/Auth/Register.php` and update the `register()` method. After `event(new Registered($user));`, add the admin notification dispatch for approval mode:

```php
use App\Mail\AdminPendingUserNotification;
use Illuminate\Support\Facades\Mail;

// After event(new Registered($user));, inside the register() method:
if ($mode === 'approval') {
    $admins = User::where('role', 'admin')->get();
    foreach ($admins as $admin) {
        Mail::to($admin)->send(new AdminPendingUserNotification($user));
    }
}
```

The full updated `register()` method becomes:

```php
public function register(): void
{
    $validated = $this->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
    ]);

    $validated['password'] = Hash::make($validated['password']);

    $mode = SiteSetting::get('registration_mode', 'closed');

    if ($mode === 'approval') {
        $validated['status'] = 'pending';
    }

    $user = User::query()->create($validated);

    event(new Registered($user));

    if ($mode === 'approval') {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Mail::to($admin)->send(new AdminPendingUserNotification($user));
        }
    }

    if ($mode === 'invitation' && $this->token) {
        Invitation::where('token', $this->token)
            ->whereNull('accepted_at')
            ->update(['accepted_at' => now()]);
    }

    Auth::login($user);

    Session::regenerate();

    $this->redirect(route('verification.notice', absolute: false), navigate: true);
}
```

- [ ] **Step 4: Add test for admin notification**

Open `tests/Feature/Auth/ApprovalTest.php` and append:

```php
use App\Mail\AdminPendingUserNotification;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use App\Livewire\Auth\Register;

test('admin receives email when user registers in approval mode', function (): void {
    Mail::fake();
    SiteSetting::set('registration_mode', 'approval');

    $admin = User::factory()->admin()->create();

    Livewire::test(Register::class)
        ->set('name', 'New Person')
        ->set('email', 'newperson@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register');

    Mail::assertSent(AdminPendingUserNotification::class, fn ($mail) => $mail->hasTo($admin->email));
});
```

- [ ] **Step 5: Run tests**

Run: `php artisan test --compact --filter=ApprovalTest`

Expected: All tests PASS.

- [ ] **Step 6: Commit**

```bash
git add app/Mail/AdminPendingUserNotification.php resources/views/emails/admin-pending-user.blade.php app/Livewire/Auth/Register.php tests/Feature/Auth/ApprovalTest.php
git commit -m "feat: notify admins when a user registers in approval mode"
```

---

## Task 10: UserApproved Mailable + Approve Action + Policy

**Files:**
- Create: `app/Mail/UserApproved.php`
- Create: `resources/views/emails/user-approved.blade.php`
- Modify: `app/Policies/UserPolicy.php`
- Modify: `app/Livewire/Users/Index.php`
- Modify: `resources/views/livewire/users/index.blade.php`
- Create: `tests/Feature/Users/UserApprovalTest.php`

- [ ] **Step 1: Write failing tests**

Run: `php artisan make:test Feature/Users/UserApprovalTest --pest --no-interaction`

Replace `tests/Feature/Users/UserApprovalTest.php`:

```php
<?php

use App\Livewire\Users\Index;
use App\Mail\UserApproved;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    SiteSetting::set('registration_mode', 'open');
    $this->admin = User::factory()->admin()->create();
    $this->pendingUser = User::factory()->pending()->create();
});

test('admin can approve a pending user', function (): void {
    Mail::fake();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('approve', $this->pendingUser->id);

    expect($this->pendingUser->fresh()->status)->toBe('active');
});

test('approved user receives an email', function (): void {
    Mail::fake();

    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->call('approve', $this->pendingUser->id);

    Mail::assertSent(UserApproved::class, fn ($mail) => $mail->hasTo($this->pendingUser->email));
});

test('non-admin cannot approve a user', function (): void {
    $editor = User::factory()->editor()->create();

    Livewire::actingAs($editor)
        ->test(Index::class)
        ->call('approve', $this->pendingUser->id)
        ->assertForbidden();
});

test('admin policy allows approve', function (): void {
    expect($this->admin->can('approve', $this->pendingUser))->toBeTrue();
});

test('editor policy denies approve', function (): void {
    $editor = User::factory()->editor()->create();
    expect($editor->can('approve', $this->pendingUser))->toBeFalse();
});

test('pending users show pending badge in users index', function (): void {
    Livewire::actingAs($this->admin)
        ->test(Index::class)
        ->assertSee('Pending');
});
```

- [ ] **Step 2: Run tests to confirm they fail**

Run: `php artisan test --compact --filter=UserApprovalTest`

Expected: All 6 tests FAIL.

- [ ] **Step 3: Create the UserApproved mailable**

Run: `php artisan make:mail UserApproved --no-interaction`

Replace `app/Mail/UserApproved.php`:

```php
<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your account has been approved — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.user-approved',
        );
    }
}
```

- [ ] **Step 4: Create the user-approved email view**

Create `resources/views/emails/user-approved.blade.php`:

```blade
<x-mail::message>
# Your account has been approved!

Hi {{ $user->name }},

Great news! Your registration on **{{ config('app.name') }}** has been approved. You can now log in and access your account.

<x-mail::button :url="route('login')">
Log In Now
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
```

- [ ] **Step 5: Add approve method to UserPolicy**

Open `app/Policies/UserPolicy.php` and add:

```php
/**
 * Determine whether the user can approve a pending registration.
 */
public function approve(User $user, User $model): bool
{
    return $user->isAdmin();
}
```

- [ ] **Step 6: Add approve action to Users/Index**

Open `app/Livewire/Users/Index.php` and add:

```php
use App\Mail\UserApproved;
use Illuminate\Support\Facades\Mail;

public function approve(int $userId): void
{
    $user = User::findOrFail($userId);

    $this->authorize('approve', $user);

    $user->update(['status' => 'active']);

    Mail::to($user)->send(new UserApproved($user));

    Flux::toast(
        heading: 'User Approved',
        text: "{$user->name} has been approved and notified.",
        variant: 'success',
    );
}
```

Add the `use Flux\Flux;` import if not already present.

- [ ] **Step 7: Update the users index blade**

Open `resources/views/livewire/users/index.blade.php`. In the Name cell, add a Pending badge after the "You" badge. In the actions dropdown, add an Approve menu item. Updated Name cell:

```blade
<flux:table.cell>
    <div class="flex items-center gap-3">
        <flux:avatar initials="{{ $user->initials() }}" size="sm" />
        <flux:text>
            {{ $user->name }}
            @if ($user->id === auth()->id())
                <flux:badge variant="info" size="sm" class="ml-2">{{ __('You') }}</flux:badge>
            @endif
            @if ($user->status === 'pending')
                <flux:badge color="yellow" size="sm" class="ml-2">{{ __('Pending') }}</flux:badge>
            @endif
        </flux:text>
    </div>
</flux:table.cell>
```

Updated actions dropdown (add Approve before Edit):

```blade
<flux:menu>
    @can('approve', $user)
        @if ($user->status === 'pending')
            <flux:menu.item icon="check-circle" wire:click="approve({{ $user->id }})" wire:confirm="{{ __('Approve this user?') }}">
                {{ __('Approve') }}
            </flux:menu.item>
            <flux:menu.separator />
        @endif
    @endcan
    @can('update', $user)
        <flux:menu.item icon="pencil" wire:navigate href="{{ route('users.edit', $user) }}">
            {{ __('Edit') }}
        </flux:menu.item>
    @endcan
    @can('delete', $user)
        @if ($user->id !== auth()->id())
            <flux:menu.separator />
            <flux:menu.item icon="trash" variant="danger" wire:click="delete({{ $user->id }})" wire:confirm="{{ __('Are you sure you want to delete this user?') }}">
                {{ __('Delete') }}
            </flux:menu.item>
        @endif
    @endcan
</flux:menu>
```

- [ ] **Step 8: Run tests to confirm they pass**

Run: `php artisan test --compact --filter=UserApprovalTest`

Expected: All 6 tests PASS.

- [ ] **Step 9: Commit**

```bash
git add app/Mail/UserApproved.php resources/views/emails/user-approved.blade.php app/Policies/UserPolicy.php app/Livewire/Users/Index.php resources/views/livewire/users/index.blade.php tests/Feature/Users/UserApprovalTest.php
git commit -m "feat: add user approval workflow with email notification"
```

---

## Task 11: Run Full Test Suite and Lint

**Files:**
- All modified PHP files (run Pint)

- [ ] **Step 1: Run all affected tests**

Run: `php artisan test --compact --filter="RegistrationModeTest|InvitationTest|ApprovalTest|UserApprovalTest|SecuritySettingsTest|RegistrationTest"`

Expected: All tests PASS.

- [ ] **Step 2: Run Pint on modified files**

Run: `vendor/bin/pint --dirty --format agent`

Expected: Any formatting issues are fixed automatically.

- [ ] **Step 3: Commit any Pint fixes**

```bash
git add -p
git commit -m "style: apply Pint formatting"
```

(Skip if no changes.)

- [ ] **Step 4: Final full suite sanity check**

Run: `php artisan test --compact`

Expected: All tests PASS with no regressions.
