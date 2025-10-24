<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Seeder;

class TaxonomySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories (hierarchical, single-select)
        $categories = \App\Models\Taxonomy::query()->create([
            'handle' => 'categories',
            'name' => 'Categories',
            'hierarchical' => true,
            'single_select' => true,
        ]);

        // Create some default categories
        $news = \App\Models\Term::query()->create([
            'taxonomy_id' => $categories->id,
            'name' => 'News',
            'slug' => 'news',
        ]);

        \App\Models\Term::query()->create([
            'taxonomy_id' => $categories->id,
            'name' => 'Company News',
            'slug' => 'company-news',
            'parent_id' => $news->id,
        ]);

        \App\Models\Term::query()->create([
            'taxonomy_id' => $categories->id,
            'name' => 'Industry News',
            'slug' => 'industry-news',
            'parent_id' => $news->id,
        ]);

        // Create Tags (non-hierarchical, multi-select)
        $tags = \App\Models\Taxonomy::query()->create([
            'handle' => 'tags',
            'name' => 'Tags',
            'hierarchical' => false,
            'single_select' => false,
        ]);

        // Create some default tags
        $tagNames = ['Featured', 'Tutorial', 'Product Update', 'Community'];
        foreach ($tagNames as $name) {
            \App\Models\Term::query()->create([
                'taxonomy_id' => $tags->id,
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
            ]);
        }
    }
}
