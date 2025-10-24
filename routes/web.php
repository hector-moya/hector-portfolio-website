<?php

use App\Livewire\Blueprints\Create as BlueprintsCreate;
use App\Livewire\Blueprints\Edit as BlueprintsEdit;
use App\Livewire\Blueprints\Index as BlueprintsIndex;
use App\Livewire\Collections\Create as CollectionsCreate;
use App\Livewire\Collections\Edit as CollectionsEdit;
use App\Livewire\Collections\Index as CollectionsIndex;
use App\Livewire\Entries\Create as EntriesCreate;
use App\Livewire\Entries\Edit as EntriesEdit;
use App\Livewire\Entries\Index as EntriesIndex;
use App\Livewire\Frontend\BlogIndex;
use App\Livewire\Frontend\BlogShow;
use App\Livewire\Frontend\ContactPage;
use App\Livewire\Frontend\Home;
use App\Livewire\Frontend\PortfolioIndex;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Taxonomies\Create as TaxonomiesCreate;
use App\Livewire\Taxonomies\Edit as TaxonomiesEdit;
use App\Livewire\Taxonomies\Index as TaxonomiesIndex;
use App\Livewire\Users\Create as UsersCreate;
use App\Livewire\Users\Edit as UsersEdit;
use App\Livewire\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// Frontend Routes (Public)
Route::get('/', Home::class)->name('home');
Route::get('/blog', BlogIndex::class)->name('blog.index');
Route::get('/blog/{slug}', BlogShow::class)->name('blog.show');
Route::get('/portfolio', PortfolioIndex::class)->name('portfolio.index');
Route::get('/contact', ContactPage::class)->name('contact');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function (): void {
    // Collections Routes
    Route::get('collections', CollectionsIndex::class)->name('collections.index');
    Route::get('collections/create', CollectionsCreate::class)->name('collections.create');
    Route::get('collections/{collection}/edit', CollectionsEdit::class)->name('collections.edit');

    // Blueprints Routes
    Route::get('blueprints', BlueprintsIndex::class)->name('blueprints.index');
    Route::get('blueprints/create', BlueprintsCreate::class)->name('blueprints.create');
    Route::get('blueprints/{blueprint}/edit', BlueprintsEdit::class)->name('blueprints.edit');

    // Entries Routes
    Route::get('entries', EntriesIndex::class)->name('entries');
    Route::get('entries/create', EntriesCreate::class)->name('entries.create');
    Route::get('entries/{entry}/edit', EntriesEdit::class)->name('entries.edit');

    // Taxonomies Routes
    Route::get('taxonomies', TaxonomiesIndex::class)->name('taxonomies.index');
    Route::get('taxonomies/create', TaxonomiesCreate::class)->name('taxonomies.create');
    Route::get('taxonomies/{taxonomy}/edit', TaxonomiesEdit::class)->name('taxonomies.edit');

    // Users Routes
    Route::get('users', UsersIndex::class)->name('users.index');
    Route::get('users/create', UsersCreate::class)->name('users.create');
    Route::get('users/{user}/edit', UsersEdit::class)->name('users.edit');

    // Settings Routes
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

require __DIR__.'/auth.php';
