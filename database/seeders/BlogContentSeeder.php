<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SectionType;
use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\User;
use Illuminate\Database\Seeder;

final class BlogContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('role', 'admin')->firstOrFail();

        // Step 1 — Blueprints
        $homeBlueprint = $this->createBlueprint('Home Page', 'home-page', 'Home page template', [
            ['label' => 'Hero', 'handle' => 'hero', 'type' => SectionType::Hero->value, 'required' => true],
            ['label' => 'Features', 'handle' => 'features', 'type' => SectionType::Features->value, 'required' => false],
            ['label' => 'Call to Action', 'handle' => 'cta', 'type' => SectionType::Cta->value, 'required' => false],
        ]);

        $blogIndexBlueprint = $this->createBlueprint('Blog Index', 'blog-index', 'Blog listing page template', [
            ['label' => 'Hero', 'handle' => 'hero', 'type' => SectionType::Hero->value, 'required' => true],
        ]);

        $blogPostBlueprint = $this->createBlueprint('Blog Post', 'blog-post', 'Blog post template', [
            ['label' => 'Hero', 'handle' => 'hero', 'type' => SectionType::Hero->value, 'required' => true],
            ['label' => 'Content', 'handle' => 'content', 'type' => SectionType::RichText->value, 'required' => true],
        ]);

        $portfolioIndexBlueprint = $this->createBlueprint('Portfolio Index', 'portfolio-index', 'Portfolio listing page template', [
            ['label' => 'Hero', 'handle' => 'hero', 'type' => SectionType::Hero->value, 'required' => true],
        ]);

        $portfolioProjectBlueprint = $this->createBlueprint('Portfolio Project', 'portfolio-project', 'Portfolio project template', [
            ['label' => 'Hero', 'handle' => 'hero', 'type' => SectionType::Hero->value, 'required' => true],
            ['label' => 'Content', 'handle' => 'content', 'type' => SectionType::RichText->value, 'required' => true],
        ]);

        // Step 2 — Collections (with parent-child links)
        Collection::query()->create(['name' => 'Home', 'slug' => 'home', 'blueprint_id' => $homeBlueprint->id]);
        $blog = Collection::query()->create(['name' => 'Blog', 'slug' => 'blog', 'blueprint_id' => $blogIndexBlueprint->id]);
        Collection::query()->create(['name' => 'Blog Posts', 'slug' => 'blog-posts', 'blueprint_id' => $blogPostBlueprint->id, 'parent_id' => $blog->id]);
        $portfolio = Collection::query()->create(['name' => 'Portfolio', 'slug' => 'portfolio', 'blueprint_id' => $portfolioIndexBlueprint->id]);
        Collection::query()->create(['name' => 'Portfolio Projects', 'slug' => 'portfolio-projects', 'blueprint_id' => $portfolioProjectBlueprint->id, 'parent_id' => $portfolio->id]);

        // Step 3 — Entries
        $this->createEntry($homeBlueprint, $admin, ['title' => 'Home', 'slug' => 'home'], [
            'hero' => [
                'title' => "Progress, not perfection. The work is never done. That's the point.",
                'subtitle' => 'A living archive of the things I build, learn, and break.',
                'content' => "The work of building software is rarely elegant. It's an ongoing loop of trial, adjustment, and acceptance.",
                'bg_image' => null,
                'cta_text' => "See what I'm building",
                'cta_url' => '/portfolio',
                'secondary_cta_text' => 'Check the journey',
                'secondary_cta_url' => '/blog',
            ],
            'features' => [
                'title' => 'What I Do',
                'items' => [],
            ],
            'cta' => [
                'title' => "Let's Work Together",
                'content' => "I'm always open to new ideas, collaborations, or a good conversation about code.",
                'cta_text' => 'Get in Touch',
                'cta_url' => '/contact',
            ],
        ]);

        $this->createEntry($blogIndexBlueprint, $admin, ['title' => 'Blog', 'slug' => 'blog'], [
            'hero' => [
                'title' => 'The Blog',
                'subtitle' => 'Thoughts on code, craft, and the process of building things.',
                'content' => "A running log of what I'm learning, building, and thinking about.",
                'bg_image' => null,
                'cta_text' => '',
                'cta_url' => '',
                'secondary_cta_text' => '',
                'secondary_cta_url' => '',
            ],
        ]);

        $blogPostsData = [
            [
                'title' => 'The Journey Begins',
                'slug' => 'the-journey-begins',
                'hero' => [
                    'title' => 'The Journey Begins',
                    'subtitle' => 'Every developer has a story of how they got started.',
                    'content' => "Here's mine.",
                    'bg_image' => null,
                    'cta_text' => '',
                    'cta_url' => '',
                    'secondary_cta_text' => '',
                    'secondary_cta_url' => '',
                ],
                'content' => [
                    'content' => '<p>Looking back on my journey into software development, it\'s interesting to see how each small decision led to where I am now. This isn\'t a story about overnight success or revolutionary breakthroughs — it\'s about consistent learning, adapting, and growing.</p><p>What started as curiosity about how websites work turned into a passion for building things that solve real problems. The challenges changed, the technologies evolved, but the fundamental drive to create and improve remained constant.</p>',
                ],
            ],
            [
                'title' => 'Building in Public',
                'slug' => 'building-in-public',
                'hero' => [
                    'title' => 'Building in Public',
                    'subtitle' => 'Why I decided to document my coding journey openly.',
                    'content' => 'Sharing the process, not just the result.',
                    'bg_image' => null,
                    'cta_text' => '',
                    'cta_url' => '',
                    'secondary_cta_text' => '',
                    'secondary_cta_url' => '',
                ],
                'content' => [
                    'content' => '<p>There\'s something powerful about building in public. It\'s not just about sharing the end result, but about documenting the process, the decisions, and even the mistakes along the way.</p><p>By sharing my work openly, I\'m not just creating a portfolio — I\'m creating a record of growth. Each commit, each design iteration, and each technical decision becomes a learning opportunity.</p>',
                ],
            ],
            [
                'title' => 'Laravel & Me',
                'slug' => 'laravel-and-me',
                'hero' => [
                    'title' => 'Laravel & Me',
                    'subtitle' => "My experience with Laravel and why it's become my go-to framework.",
                    'content' => 'It felt like discovering a well-organised toolbox.',
                    'bg_image' => null,
                    'cta_text' => '',
                    'cta_url' => '',
                    'secondary_cta_text' => '',
                    'secondary_cta_url' => '',
                ],
                'content' => [
                    'content' => '<p>When I first encountered Laravel, it felt like discovering a well-organized toolbox after years of working with scattered tools. Its elegant syntax and robust ecosystem transformed how I approach web development.</p><p>What I appreciate most about Laravel isn\'t just its features — it\'s the philosophy behind them. The framework emphasises developer experience without compromising on capabilities.</p>',
                ],
            ],
        ];

        foreach ($blogPostsData as $post) {
            $this->createEntry($blogPostBlueprint, $admin, ['title' => $post['title'], 'slug' => $post['slug'], 'published_at' => now()->subDays(random_int(1, 30))], [
                'hero' => $post['hero'],
                'content' => $post['content'],
            ]);
        }

        $this->createEntry($portfolioIndexBlueprint, $admin, ['title' => 'Portfolio', 'slug' => 'portfolio'], [
            'hero' => [
                'title' => 'My Work',
                'subtitle' => "A selection of projects I've built and contributed to.",
                'content' => 'Each project is a snapshot of where I was technically and creatively at that moment in time.',
                'bg_image' => null,
                'cta_text' => '',
                'cta_url' => '',
                'secondary_cta_text' => '',
                'secondary_cta_url' => '',
            ],
        ]);

        $portfolioProjectsData = [
            [
                'title' => 'E-Commerce Platform Redesign',
                'slug' => 'ecommerce-platform-redesign',
                'hero' => [
                    'title' => 'E-Commerce Platform Redesign',
                    'subtitle' => 'Improving user experience and conversion rates.',
                    'content' => 'Redesigned a major e-commerce platform with modern design patterns and responsive layouts.',
                    'bg_image' => null,
                    'cta_text' => 'View Project',
                    'cta_url' => 'https://example.com/project1',
                    'secondary_cta_text' => 'GitHub',
                    'secondary_cta_url' => 'https://github.com/example/project1',
                ],
                'content' => [
                    'content' => '<p>Redesigned a major e-commerce platform to improve user experience and increase conversion rates. Implemented modern design patterns and responsive layouts.</p><p>Technologies used: Laravel, Vue.js, Tailwind CSS, MySQL.</p>',
                ],
            ],
            [
                'title' => 'Mobile Banking Application',
                'slug' => 'mobile-banking-application',
                'hero' => [
                    'title' => 'Mobile Banking Application',
                    'subtitle' => 'Secure banking with biometric authentication.',
                    'content' => 'Developed a secure mobile banking app with real-time transaction monitoring.',
                    'bg_image' => null,
                    'cta_text' => 'View Project',
                    'cta_url' => 'https://example.com/project2',
                    'secondary_cta_text' => '',
                    'secondary_cta_url' => '',
                ],
                'content' => [
                    'content' => '<p>Developed a secure mobile banking application with biometric authentication and real-time transaction monitoring.</p><p>Technologies used: React Native, Node.js, PostgreSQL, AWS.</p>',
                ],
            ],
            [
                'title' => 'Healthcare Management System',
                'slug' => 'healthcare-management-system',
                'hero' => [
                    'title' => 'Healthcare Management System',
                    'subtitle' => 'Patient records, scheduling, and billing in one place.',
                    'content' => 'A comprehensive system built for a multi-location healthcare provider.',
                    'bg_image' => null,
                    'cta_text' => 'View Project',
                    'cta_url' => 'https://example.com/project3',
                    'secondary_cta_text' => 'GitHub',
                    'secondary_cta_url' => 'https://github.com/example/project3',
                ],
                'content' => [
                    'content' => '<p>Built a comprehensive healthcare management system for patient records, appointment scheduling, and billing.</p><p>Technologies used: Laravel, Livewire, MySQL, Redis.</p>',
                ],
            ],
        ];

        foreach ($portfolioProjectsData as $project) {
            $this->createEntry($portfolioProjectBlueprint, $admin, ['title' => $project['title'], 'slug' => $project['slug']], [
                'hero' => $project['hero'],
                'content' => $project['content'],
            ]);
        }
    }

    /**
     * @param  array<int, array{label: string, handle: string, type: string, required?: bool}>  $fieldDefs
     */
    private function createBlueprint(string $name, string $slug, string $description, array $fieldDefs): Blueprint
    {
        $blueprint = Blueprint::query()->create(['name' => $name, 'slug' => $slug, 'description' => $description]);
        $tab = $blueprint->tabs()->create(['name' => 'Content', 'handle' => 'content', 'sort_order' => 0, 'blueprint_id' => $blueprint->id]);
        $section = $tab->sections()->create(['blueprint_id' => $blueprint->id, 'name' => 'Content', 'handle' => 'content', 'sort_order' => 0]);

        foreach ($fieldDefs as $order => $def) {
            $section->fields()->create([
                'blueprint_id' => $blueprint->id,
                'type' => $def['type'],
                'label' => $def['label'],
                'handle' => $def['handle'],
                'is_required' => $def['required'] ?? false,
                'order' => $order,
            ]);
        }

        return $blueprint->load('fields');
    }

    /**
     * @param  array<string, mixed>  $meta
     * @param  array<string, mixed>  $fieldValues
     */
    private function createEntry(Blueprint $blueprint, User $author, array $meta, array $fieldValues): Entry
    {
        $entry = Entry::query()->create([
            'blueprint_id' => $blueprint->id,
            'author_id' => $author->id,
            'title' => $meta['title'],
            'slug' => $meta['slug'],
            'status' => $meta['status'] ?? 'published',
            'published_at' => $meta['published_at'] ?? now(),
            'seo_title' => $meta['seo_title'] ?? null,
            'seo_description' => $meta['seo_description'] ?? null,
        ]);

        foreach ($blueprint->fields as $field) {
            $value = $fieldValues[$field->handle] ?? SectionType::from($field->type)->defaultData();
            $element = $entry->elements()->create([
                'field_id' => $field->id,
                'handle' => $field->handle,
            ]);
            $element->setElementValue($value);
            $element->save();
        }

        return $entry;
    }
}
