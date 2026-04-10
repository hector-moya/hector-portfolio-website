<?php

declare(strict_types=1);

use App\Ai\Agents\LandingPageWizardAgent;
use App\Livewire\LandingPages\AiWizard;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->create(['role' => 'admin']));
});

test('landing page ai wizard page loads', function () {
    $this->get(route('landing-pages.ai-wizard'))->assertOk();
});

test('generate sets proposal from ai agent', function () {
    LandingPageWizardAgent::fake([[
        'sections' => [
            [
                '_id' => 'a1b2c3',
                'type' => 'hero',
                'data' => [
                    'title' => 'Welcome',
                    'subtitle' => 'We build things',
                    'content' => '',
                    'cta_text' => 'Get Started',
                    'cta_url' => '/',
                    'secondary_cta_text' => '',
                    'secondary_cta_url' => '',
                    'bg_image' => null,
                    'image' => null,
                    'images' => [],
                    'image_position' => 'left',
                    'alignment' => 'left',
                    'items' => [],
                ],
            ],
        ],
    ]]);

    Livewire::test(AiWizard::class)
        ->set('name', 'About Us')
        ->set('slug', 'about-us')
        ->set('description', 'A page about our company and team')
        ->call('generate')
        ->assertSet('step', 'review')
        ->assertCount('proposal', 1);
});

test('remove section removes it from proposal', function () {
    LandingPageWizardAgent::fake([[
        'sections' => [
            ['_id' => 'a1', 'type' => 'hero', 'data' => ['title' => 'Hero', 'subtitle' => '', 'content' => '', 'cta_text' => '', 'cta_url' => '', 'secondary_cta_text' => '', 'secondary_cta_url' => '', 'bg_image' => null, 'image' => null, 'images' => [], 'image_position' => 'left', 'alignment' => 'left', 'items' => []]],
            ['_id' => 'b2', 'type' => 'cta',  'data' => ['title' => 'CTA',  'subtitle' => '', 'content' => '', 'cta_text' => '', 'cta_url' => '', 'secondary_cta_text' => '', 'secondary_cta_url' => '', 'bg_image' => null, 'image' => null, 'images' => [], 'image_position' => 'left', 'alignment' => 'left', 'items' => []]],
        ],
    ]]);

    Livewire::test(AiWizard::class)
        ->set('name', 'Test Page')
        ->set('description', 'A test landing page for our company')
        ->call('generate')
        ->call('removeSection', 0)
        ->assertCount('proposal', 1)
        ->assertSet('proposal.0.type', 'cta');
});

test('save creates collection blueprint and entry as draft', function () {
    LandingPageWizardAgent::fake([[
        'sections' => [
            [
                '_id' => 'a1b2c3',
                'type' => 'hero',
                'data' => [
                    'title' => 'Services',
                    'subtitle' => '',
                    'content' => '',
                    'cta_text' => '',
                    'cta_url' => '',
                    'secondary_cta_text' => '',
                    'secondary_cta_url' => '',
                    'bg_image' => null,
                    'image' => null,
                    'images' => [],
                    'image_position' => 'left',
                    'alignment' => 'left',
                    'items' => [],
                ],
            ],
        ],
    ]]);

    Livewire::test(AiWizard::class)
        ->set('name', 'Services')
        ->set('description', 'Our services page')
        ->call('generate')
        ->call('save')
        ->assertRedirectContains('/entries/');

    $collection = Collection::where('slug', 'services')->first();
    expect($collection)->not->toBeNull();
    expect($collection->settings['type'])->toBe('single');

    $blueprint = Blueprint::find($collection->blueprint_id);
    expect($blueprint)->not->toBeNull();
    expect($blueprint->slug)->toBe('services-blueprint');

    $entry = Entry::where('slug', 'services')->first();
    expect($entry)->not->toBeNull();
    expect($entry->status)->toBe('draft');

    expect($collection->fresh()->blueprint_id)->toBe($blueprint->id);
});

it('denies access to viewer role users', function () {
    $viewer = User::factory()->create(['role' => 'viewer']);

    Livewire::actingAs($viewer)
        ->test(AiWizard::class)
        ->assertForbidden();
});

test('unauthenticated users are redirected to login', function () {
    auth()->logout();
    $this->get(route('landing-pages.ai-wizard'))->assertRedirect(route('login'));
});
