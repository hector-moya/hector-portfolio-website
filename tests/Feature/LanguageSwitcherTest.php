<?php

declare(strict_types=1);

use App\Livewire\LanguageSwitcher;
use Illuminate\Support\Facades\App;
use Livewire\Livewire;

test('language switcher can be rendered', function () {
    $component = Livewire::test(LanguageSwitcher::class);

    $component->assertStatus(200);
});

test('current locale is set correctly', function () {
    App::setLocale('es');

    $component = Livewire::test(LanguageSwitcher::class);

    expect($component->get('currentLocale'))->toBe('es');
});

test('switching language updates locale and session', function () {

    $component = Livewire::test(LanguageSwitcher::class, [
        'currentRoute' => 'blueprints.index',
    ])
        ->call('switchLocale', 'es');

    expect(App::getLocale())->toBe('es')
        ->and(session('locale'))->toBe('es')
        ->and($component->get('currentLocale'))->toBe('es');
});

test('switching to invalid locale does nothing', function () {
    App::setLocale('en');

    $component = Livewire::test(LanguageSwitcher::class)
        ->call('switchLocale', 'fr');

    expect(App::getLocale())->toBe('en')
        ->and(session('locale'))->toBeNull()
        ->and($component->get('currentLocale'))->toBe('en');
});

test('switching language dispatches event', function () {
    Livewire::test(LanguageSwitcher::class, [
        'currentRoute' => 'blueprints.index',
    ])
        ->call('switchLocale', 'es')
        ->assertDispatched('language-changed');
});

test('language names are displayed correctly', function () {
    Livewire::test(LanguageSwitcher::class)
        ->assertSee('English')
        ->assertSee('Español');
});

test('translations are loaded correctly', function () {
    App::setLocale('es');

    expect(__('Dashboard'))->toBe('Panel')
        ->and(__('Collections'))->toBe('Colecciones')
        ->and(__('Users'))->toBe('Usuarios');
});
