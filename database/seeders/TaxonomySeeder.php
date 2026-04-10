<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class TaxonomySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Categories (hierarchical, single-select)
        $categories = Taxonomy::query()->create([
            'handle' => 'categories',
            'name' => 'Categories',
            'hierarchical' => true,
            'single_select' => true,
        ]);

        // Create some default categories
        $news = Term::query()->create([
            'taxonomy_id' => $categories->id,
            'name' => 'News',
            'slug' => 'news',
        ]);

        Term::query()->create([
            'taxonomy_id' => $categories->id,
            'name' => 'Company News',
            'slug' => 'company-news',
            'parent_id' => $news->id,
        ]);

        Term::query()->create([
            'taxonomy_id' => $categories->id,
            'name' => 'Industry News',
            'slug' => 'industry-news',
            'parent_id' => $news->id,
        ]);

        // Create Tags (non-hierarchical, multi-select)
        $tags = Taxonomy::query()->create([
            'handle' => 'tags',
            'name' => 'Tags',
            'hierarchical' => false,
            'single_select' => false,
        ]);

        // Create some default tags
        $tagNames = ['Featured', 'Tutorial', 'Product Update', 'Community'];
        foreach ($tagNames as $name) {
            Term::query()->create([
                'taxonomy_id' => $tags->id,
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }
    }
}
