<?php

use App\Ai\Agents\BlueprintWizardAgent;
use App\Livewire\Blueprints\AiWizard;
use App\Models\Blueprint;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    actingAs(User::factory()->create(['role' => 'admin']));
});

test('blueprint ai wizard page loads', function () {
    $this->get(route('blueprints.ai-wizard'))->assertOk();
});

test('generate step calls the AI agent and sets proposal', function () {
    BlueprintWizardAgent::fake([[
        'name' => 'Blog Post',
        'slug' => 'blog-post',
        'description' => 'A blog post blueprint',
        'tabs' => [
            [
                'name' => 'Content',
                'handle' => 'content',
                'sections' => [
                    [
                        'name' => 'Main',
                        'handle' => 'main',
                        'fields' => [
                            ['type' => 'richtext', 'label' => 'Content', 'handle' => 'content', 'instructions' => '', 'is_required' => false],
                        ],
                    ],
                ],
            ],
        ],
    ]]);

    Livewire::test(AiWizard::class)
        ->set('description', 'A blog post blueprint')
        ->call('generate')
        ->assertSet('step', 'review')
        ->assertSet('proposal.name', 'Blog Post');
});

test('save creates blueprint with tabs sections and fields', function () {
    BlueprintWizardAgent::fake([[
        'name' => 'Portfolio Item',
        'slug' => 'portfolio-item',
        'description' => 'A portfolio item',
        'tabs' => [
            [
                'name' => 'Content',
                'handle' => 'content',
                'sections' => [
                    [
                        'name' => 'Details',
                        'handle' => 'details',
                        'fields' => [
                            ['type' => 'text', 'label' => 'Title', 'handle' => 'title', 'instructions' => '', 'is_required' => true],
                            ['type' => 'textarea', 'label' => 'Summary', 'handle' => 'summary', 'instructions' => '', 'is_required' => false],
                        ],
                    ],
                ],
            ],
        ],
    ]]);

    Livewire::test(AiWizard::class)
        ->set('description', 'A portfolio item blueprint')
        ->call('generate')
        ->call('save')
        ->assertRedirectContains('/blueprints/');

    $blueprint = Blueprint::where('slug', 'portfolio-item')->first();
    expect($blueprint)->not->toBeNull();
    expect($blueprint->tabs)->toHaveCount(1);
    expect($blueprint->tabs->first()->sections)->toHaveCount(1);
    expect($blueprint->tabs->first()->sections->first()->fields)->toHaveCount(2);
});

test('remove tab removes it from proposal', function () {
    Livewire::test(AiWizard::class)
        ->set('step', 'review')
        ->set('proposal', [
            'name' => 'Test', 'slug' => 'test', 'description' => 'test',
            'tabs' => [
                ['name' => 'Tab A', 'handle' => 'tab-a', 'sections' => []],
                ['name' => 'Tab B', 'handle' => 'tab-b', 'sections' => []],
            ],
        ])
        ->call('removeTab', 0)
        ->assertSet('proposal.tabs.0.name', 'Tab B');
});
