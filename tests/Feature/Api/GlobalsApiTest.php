<?php

declare(strict_types=1);

use App\Models\GlobalSet;
use App\Models\GlobalVariable;

test('globals api keys variables by their handle', function (): void {
    $set = GlobalSet::factory()->create(['handle' => 'branding', 'name' => 'Branding']);

    GlobalVariable::query()->create([
        'global_set_id' => $set->id,
        'handle' => 'site_name',
        'value' => 'Acme Studio',
    ]);
    GlobalVariable::query()->create([
        'global_set_id' => $set->id,
        'handle' => 'tagline',
        'value' => 'We build things',
    ]);

    $this->getJson('/api/v1/globals')
        ->assertOk()
        ->assertJsonPath('data.0.handle', 'branding')
        ->assertJsonPath('data.0.variables.site_name', 'Acme Studio')
        ->assertJsonPath('data.0.variables.tagline', 'We build things');
});

test('globals api show returns a single set keyed by handle', function (): void {
    $set = GlobalSet::factory()->create(['handle' => 'social', 'name' => 'Social']);

    GlobalVariable::query()->create([
        'global_set_id' => $set->id,
        'handle' => 'twitter',
        'value' => '@acme',
    ]);

    $this->getJson('/api/v1/globals/social')
        ->assertOk()
        ->assertJsonPath('data.variables.twitter', '@acme');
});

test('globals api show returns 404 for an unknown handle', function (): void {
    $this->getJson('/api/v1/globals/missing')->assertNotFound();
});
