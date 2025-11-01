<?php

use App\Models\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate existing blueprints
        Blueprint::all()->each(function ($blueprint) {
            try {
                // Get the blueprint type (default to 'collection' if not set)
                $type = $blueprint->type ?? 'collection';

                if ($type === 'collection') {
                    // Create main tab
                    $mainTab = $blueprint->tabs()->create([
                        'name' => 'Main Content',
                        'handle' => 'main',
                        'sort_order' => 0,
                    ]);

                    if ($mainTab) {
                        // Create main content section
                        $blueprint->sections()->create([
                            'name' => 'Content',
                            'handle' => 'content',
                            'tab_id' => $mainTab->id,
                            'sort_order' => 0,
                        ]);
                    }

                    // Create sidebar tab
                    $sidebarTab = $blueprint->tabs()->create([
                        'name' => 'Sidebar',
                        'handle' => 'sidebar',
                        'sort_order' => 1,
                    ]);

                    if ($sidebarTab) {
                        // Create sidebar sections
                        $blueprint->sections()->create([
                            'name' => 'Meta',
                            'handle' => 'meta',
                            'tab_id' => $sidebarTab->id,
                            'sort_order' => 0,
                        ]);

                        $blueprint->sections()->create([
                            'name' => 'SEO',
                            'handle' => 'seo',
                            'tab_id' => $sidebarTab->id,
                            'sort_order' => 1,
                        ]);
                    }
                } elseif ($type === 'navigation') {
                    // Create navigation section
                    $blueprint->sections()->create([
                        'name' => 'Navigation Settings',
                        'handle' => 'settings',
                        'instructions' => 'Configure how navigation items should behave',
                        'sort_order' => 0,
                    ]);
                } elseif ($type === 'asset') {
                    // Create asset section
                    $blueprint->sections()->create([
                        'name' => 'Asset Information',
                        'handle' => 'info',
                        'instructions' => 'Metadata for this asset type',
                        'sort_order' => 0,
                    ]);
                } elseif ($type === 'user') {
                    // Create profile tab
                    $profileTab = $blueprint->tabs()->create([
                        'name' => 'Profile',
                        'handle' => 'profile',
                        'sort_order' => 0,
                    ]);

                    if ($profileTab) {
                        $blueprint->sections()->create([
                            'name' => 'Basic Information',
                            'handle' => 'basic_info',
                            'tab_id' => $profileTab->id,
                            'sort_order' => 0,
                        ]);
                    }

                    // Create preferences tab
                    $preferencesTab = $blueprint->tabs()->create([
                        'name' => 'Preferences',
                        'handle' => 'preferences',
                        'sort_order' => 1,
                    ]);

                    if ($preferencesTab) {
                        $blueprint->sections()->create([
                            'name' => 'Preferences',
                            'handle' => 'user_preferences',
                            'tab_id' => $preferencesTab->id,
                            'sort_order' => 0,
                        ]);
                    }
                }

                // Move existing elements to first available section
                $firstSection = $blueprint->sections()->first();
                if ($firstSection) {
                    $blueprint->elements()->update([
                        'section_id' => $firstSection->id,
                    ]);
                }
            } catch (\Exception $e) {
                // Log the error but continue with other blueprints
                \Log::error("Error migrating blueprint {$blueprint->id}: " . $e->getMessage());
            }
        });
    }

    public function down(): void
    {
        // Nothing to do here since the main migration handles table cleanup
    }
};
