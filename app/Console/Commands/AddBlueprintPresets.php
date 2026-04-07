<?php

namespace App\Console\Commands;

use App\Models\Blueprint;
use App\Models\Section;
use App\Services\BlueprintSectionPresets;
use Illuminate\Console\Command;

class AddBlueprintPresets extends Command
{
    protected $signature = 'blueprint:add-presets {blueprint : The ID of the blueprint} {preset : The preset to add (seo|navigation|asset|user-profile)}';

    protected $description = 'Add a preset section with predefined fields to a blueprint';

    public function handle(): int
    {
        $blueprint = Blueprint::query()->findOrFail($this->argument('blueprint'));
        $preset = $this->argument('preset');

        /** @var Section $section */
        $section = $blueprint->sections()->create([
            'name' => match ($preset) {
                'seo' => 'SEO Settings',
                'navigation' => 'Navigation Settings',
                'asset' => 'Asset Information',
                'user-profile' => 'Profile Information',
                default => 'New Section',
            },
            'handle' => $preset,
            'instructions' => 'Preset fields for '.$preset,
            'sort_order' => $blueprint->sections()->max('sort_order') + 1,
        ]);

        BlueprintSectionPresets::addPreset($section, $preset);

        $this->info('Added preset section: '.$section->name);

        return Command::SUCCESS;
    }
}
