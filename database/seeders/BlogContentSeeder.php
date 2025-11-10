<?php

namespace Database\Seeders;

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\Entry;
use App\Models\EntryElement;
use App\Models\Field;
use App\Models\User;
use App\Enums\FieldType;
use App\Livewire\Actions\Blueprints\CreateBlueprint;
use App\Services\FieldTypeRegistry;
use Illuminate\Database\Seeder;

class BlogContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create admin user
        $admin = User::query()->where('role', 'admin')->first();

        // Create Blueprints
        $pageBlueprint = $this->createHomePageBlueprint();
        $blogBlueprint = $this->createBlogBlueprint();
        $portfolioBlueprint = $this->createPortfolioBlueprint();
        $contactBlueprint = $this->createContactBlueprint();

        // Create Collections
        Collection::query()->create([
            'name' => 'Pages',
            'slug' => 'pages',
            'description' => 'Static pages for the website',
            'blueprint_id' => $pageBlueprint->id,
        ]);

        Collection::query()->create([
            'name' => 'Blog',
            'slug' => 'blog',
            'description' => 'Blog posts and articles',
            'blueprint_id' => $blogBlueprint->id,
        ]);

        Collection::query()->create([
            'name' => 'Portfolio',
            'slug' => 'portfolio',
            'description' => 'Portfolio projects and work samples',
            'blueprint_id' => $portfolioBlueprint->id,
        ]);

        Collection::query()->create([
            'name' => 'Contact',
            'slug' => 'contact',
            'description' => 'Contact page content',
            'blueprint_id' => $contactBlueprint->id,
        ]);

        // Create Entries
        $this->createLandingPage($pageBlueprint, $admin);
        $this->createBlogPosts($blogBlueprint, $admin);
        $this->createPortfolioItems($portfolioBlueprint, $admin);
        $this->createContactPage($contactBlueprint, $admin);
    }

    private function createHomePageBlueprint(): Blueprint
    {
        return (new CreateBlueprint)->create([
            'name' => 'Home Page',
            'slug' => 'home-page',
            'description' => 'Home page template',
            'tabs' => [
                [
                    'name' => 'Hero',
                    'handle' => 'hero',
                    'sortOrder' => 0,
                    'sections' => [
                        [
                            'name' => 'Hero Content',
                            'handle' => 'hero_content',
                            'sortOrder' => 0,
                            'instructions' => '',
                            'fields' => [
                                [
                                    'label' => 'Hero Title',
                                    'handle' => 'hero_title',
                                    'type' => 'text',
                                    'sortOrder' => 0,
                                    'instructions' => 'The main title in the hero section',
                                    'is_required' => true,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('text'),
                                ],
                                [
                                    'label' => 'Hero Subtitle',
                                    'handle' => 'hero_subtitle',
                                    'type' => 'text',
                                    'sortOrder' => 1,
                                    'instructions' => 'A subtitle to display below the hero title',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('text'),
                                ],
                                [
                                    'label' => 'Hero Image',
                                    'handle' => 'hero_image',
                                    'type' => 'image',
                                    'sortOrder' => 2,
                                    'instructions' => 'Background image for the hero section',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('image'),

                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Main',
                    'handle' => 'main',
                    'sortOrder' => 1,
                    'sections' => [
                        [
                            'name' => 'Content',
                            'handle' => 'content',
                            'sortOrder' => 0,
                            'instructions' => '',
                            'fields' => [
                                [
                                    'label' => 'Content',
                                    'handle' => 'content',
                                    'type' => 'richtext',
                                    'sortOrder' => 0,
                                    'instructions' => 'The main content of the page',
                                    'is_required' => true,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('rich_text'),

                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Call to Action',
                    'handle' => 'cta',
                    'sortOrder' => 2,
                    'sections' => [
                        [
                            'name' => 'Primary CTA',
                            'handle' => 'primary_cta',
                            'sortOrder' => 0,
                            'instructions' => 'Configure the primary call-to-action button',
                            'fields' => [
                                [
                                    'label' => 'Button Text',
                                    'handle' => 'cta_text_primary',
                                    'type' => 'text',
                                    'sortOrder' => 0,
                                    'instructions' => 'Text to display on the primary button',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('text'),

                                ],
                                [
                                    'label' => 'Button URL',
                                    'handle' => 'cta_url_primary',
                                    'type' => 'url',
                                    'sortOrder' => 1,
                                    'instructions' => 'Where the primary button should link to',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('url'),

                                ],
                            ],
                        ],
                        [
                            'name' => 'Secondary CTA',
                            'handle' => 'secondary_cta',
                            'sortOrder' => 1,
                            'instructions' => 'Configure the secondary call-to-action button',
                            'fields' => [
                                [
                                    'label' => 'Button Text',
                                    'handle' => 'cta_text_secondary',
                                    'type' => 'text',
                                    'sortOrder' => 0,
                                    'instructions' => 'Text to display on the secondary button',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('text'),

                                ],
                                [
                                    'label' => 'Button URL',
                                    'handle' => 'cta_url_secondary',
                                    'type' => 'url',
                                    'sortOrder' => 1,
                                    'instructions' => 'Where the secondary button should link to',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('url'),

                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function createBlogBlueprint(): Blueprint
    {
        return (new CreateBlueprint)->create([
            'name' => 'Blog Post',
            'slug' => 'blog-post',
            'description' => 'Blog post template',
            'tabs' => [
                [
                    'name' => 'Main',
                    'handle' => 'main',
                    'sortOrder' => 0,
                    'sections' => [
                        [
                            'name' => 'Content',
                            'handle' => 'content',
                            'sortOrder' => 0,
                            'instructions' => '',
                            'fields' => [
                                [
                                    'label' => 'Title',
                                    'handle' => 'title',
                                    'type' => 'text',
                                    'sortOrder' => 0,
                                    'instructions' => 'The title of the blog post',
                                    'is_required' => true,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('text'),

                                ],
                                [
                                    'label' => 'Content',
                                    'handle' => 'content',
                                    'type' => 'richtext',
                                    'sortOrder' => 1,
                                    'instructions' => 'The main content of the blog post',
                                    'is_required' => true,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('rich_text'),

                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Featured',
                    'handle' => 'featured',
                    'sortOrder' => 1,
                    'sections' => [
                        [
                            'name' => 'Featured Image',
                            'handle' => 'featured_image',
                            'sortOrder' => 0,
                            'instructions' => 'Configure the featured image for this post',
                            'fields' => [
                                [
                                    'label' => 'Featured Image',
                                    'handle' => 'featured_image',
                                    'type' => 'image',
                                    'sortOrder' => 0,
                                    'instructions' => 'The main image for this blog post',
                                    'is_required' => true,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('image'),

                                ],
                                [
                                    'label' => 'Excerpt',
                                    'handle' => 'excerpt',
                                    'type' => 'textarea',
                                    'sortOrder' => 1,
                                    'instructions' => 'A brief summary of the blog post',
                                    'is_required' => true,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('textarea'),

                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }


    private function createPortfolioBlueprint(): Blueprint
    {
        return (new CreateBlueprint)->create([
            'name' => 'Portfolio Project',
            'slug' => 'portfolio-project',
            'description' => 'Portfolio project template',
            'tabs' => [
                [
                    'name' => 'Main',
                    'handle' => 'main',
                    'sortOrder' => 0,
                    'sections' => [
                        [
                            'name' => 'Content',
                            'handle' => 'content',
                            'sortOrder' => 0,
                            'instructions' => '',
                            'fields' => [
                                [
                                    'label' => 'Project Title',
                                    'handle' => 'title',
                                    'type' => 'text',
                                    'sortOrder' => 0,
                                    'instructions' => 'The name of your project',
                                    'is_required' => true,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('text'),

                                ],
                                [
                                    'label' => 'Description',
                                    'handle' => 'description',
                                    'type' => 'richtext',
                                    'sortOrder' => 1,
                                    'instructions' => 'A detailed description of the project',
                                    'is_required' => true,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('rich_text'),

                                ],
                                [
                                    'label' => 'Project Image',
                                    'handle' => 'project_image',
                                    'type' => 'image',
                                    'sortOrder' => 2,
                                    'instructions' => 'The main image showcasing your project',
                                    'is_required' => true,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('image'),

                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Project Details',
                    'handle' => 'project_details',
                    'sortOrder' => 1,
                    'sections' => [
                        [
                            'name' => 'Links',
                            'handle' => 'links',
                            'sortOrder' => 0,
                            'instructions' => 'External links for this project',
                            'fields' => [
                                [
                                    'label' => 'Project URL',
                                    'handle' => 'project_url',
                                    'type' => 'url',
                                    'sortOrder' => 0,
                                    'instructions' => 'Link to the live project',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('url'),

                                ],
                                [
                                    'label' => 'GitHub URL',
                                    'handle' => 'github_url',
                                    'type' => 'url',
                                    'sortOrder' => 1,
                                    'instructions' => 'Link to the project repository',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('url'),

                                ],
                            ],
                        ],
                        [
                            'name' => 'Technologies',
                            'handle' => 'technologies',
                            'sortOrder' => 1,
                            'instructions' => 'Technologies used in this project',
                            'fields' => [
                                [
                                    'label' => 'Technologies',
                                    'handle' => 'technologies',
                                    'type' => 'text',
                                    'sortOrder' => 0,
                                    'instructions' => 'List of technologies used',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('text'),

                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function createContactBlueprint(): Blueprint
    {
        return (new CreateBlueprint)->create([
            'name' => 'Contact Page',
            'slug' => 'contact-page',
            'description' => 'Contact page template',
            'tabs' => [
                [
                    'name' => 'Main',
                    'handle' => 'main',
                    'sortOrder' => 0,
                    'sections' => [
                        [
                            'name' => 'Content',
                            'handle' => 'content',
                            'sortOrder' => 0,
                            'instructions' => '',
                            'fields' => [
                                [
                                    'label' => 'Heading',
                                    'handle' => 'heading',
                                    'type' => 'text',
                                    'sortOrder' => 0,
                                    'instructions' => 'The main heading for the contact page',
                                    'is_required' => true,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('text'),

                                ],
                                [
                                    'label' => 'Subheading',
                                    'handle' => 'subheading',
                                    'type' => 'text',
                                    'sortOrder' => 1,
                                    'instructions' => 'A brief message below the heading',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('text'),

                                ],
                                [
                                    'label' => 'Contact Instructions',
                                    'handle' => 'instructions',
                                    'type' => 'richtext',
                                    'sortOrder' => 2,
                                    'instructions' => 'Instructions or additional content for the contact page',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('rich_text'),

                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'name' => 'Contact Details',
                    'handle' => 'contact_details',
                    'sortOrder' => 1,
                    'sections' => [
                        [
                            'name' => 'Social Links',
                            'handle' => 'social_links',
                            'sortOrder' => 0,
                            'instructions' => 'Your social media and contact links',
                            'fields' => [
                                [
                                    'label' => 'Email',
                                    'handle' => 'email',
                                    'type' => 'email',
                                    'sortOrder' => 0,
                                    'instructions' => 'Your contact email address',
                                    'is_required' => true,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('email'),

                                ],
                                [
                                    'label' => 'LinkedIn URL',
                                    'handle' => 'linkedin_url',
                                    'type' => 'url',
                                    'sortOrder' => 1,
                                    'instructions' => 'Your LinkedIn profile URL',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('url'),

                                ],
                                [
                                    'label' => 'GitHub URL',
                                    'handle' => 'github_url',
                                    'type' => 'url',
                                    'sortOrder' => 2,
                                    'instructions' => 'Your GitHub profile URL',
                                    'is_required' => false,
                                    'config' => app(FieldTypeRegistry::class)->defaultConfigFor('url'),

                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
    private function createLandingPage(Blueprint $blueprint, User $admin): void
    {
        $entry = Entry::query()->create([
            'title' => 'Home - Landing Page',
            'slug' => 'home',
            'blueprint_id' => $blueprint->id,
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $fields = [
            'hero_title' => "Progress, not perfection. The work is never done. That's the point.",
            'hero_subtitle' => 'A living archive of the things I build, learn, and break.',
            'hero_image' => 'https://picsum.photos/seed/hero/1920/1080',
            'content' => "The work of building software is rarely elegant. It's an ongoing loop of trial, adjustment, and acceptance - an exercise in problem-solving where every solution creates new questions. This site is where that loop continues - a place to record what I've built, what I've learned, and the patterns I keep chasing. The goal isn't to impress; it's to understand.",
            'cta_text_primary' => "See what I'm building",
            'cta_url_primary' => '/portfolio',
            'cta_text_secondary' => 'Check the journey',
            'cta_url_secondary' => '/blog',
        ];

        foreach ($blueprint->fields as $field) {
            EntryElement::query()->create([
                'entry_id' => $entry->id,
                'field_id' => $field->id,
                'handle' => $field->handle,
                'value' => $fields[$field->handle] ?? '',
            ]);
        }
    }

    private function createBlogPosts(Blueprint $blueprint, User $admin): void
    {
        $posts = [
            [
                'title' => 'The Journey Begins',
                'slug' => 'the-journey-begins',
                'featured_image' => 'https://picsum.photos/seed/blog1/1200/630',
                'excerpt' => "Every developer has a story of how they got started. Here's mine.",
                'content' => "Looking back on my journey into software development, it's interesting to see how each small decision led to where I am now. This isn't a story about overnight success or revolutionary breakthroughs—it's about consistent learning, adapting, and growing.\n\nWhat started as curiosity about how websites work turned into a passion for building things that solve real problems. The challenges changed, the technologies evolved, but the fundamental drive to create and improve remained constant.\n\nThis blog will document my ongoing journey, sharing both successes and setbacks. Because sometimes the most valuable lessons come from what didn't work.",
            ],
            [
                'title' => 'Building in Public',
                'slug' => 'building-in-public',
                'featured_image' => 'https://picsum.photos/seed/blog2/1200/630',
                'excerpt' => 'Why I decided to document my coding journey and build this site in public.',
                'content' => "There's something powerful about building in public. It's not just about sharing the end result, but about documenting the process, the decisions, and even the mistakes along the way. This website itself is an exercise in that philosophy.\n\nBy sharing my work openly, I'm not just creating a portfolio—I'm creating a record of growth. Each commit, each design iteration, and each technical decision becomes a learning opportunity, not just for me, but potentially for others on similar paths.\n\nThis approach brings accountability and encourages better practices. When you know your work will be public, you think more carefully about your choices and document your decisions more thoroughly.",
            ],
            [
                'title' => 'Laravel & Me',
                'slug' => 'laravel-and-me',
                'featured_image' => 'https://picsum.photos/seed/blog3/1200/630',
                'excerpt' => "My experience with Laravel and why it's become my go-to framework.",
                'content' => "When I first encountered Laravel, it felt like discovering a well-organized toolbox after years of working with scattered tools. Its elegant syntax and robust ecosystem transformed how I approach web development.\n\nWhat I appreciate most about Laravel isn't just its features—it's the philosophy behind them. The framework emphasizes developer experience without compromising on capabilities. It makes common tasks simple while keeping complex operations possible.\n\nThis site is built with Laravel, and each new project helps me discover more about what the framework can do. It's not just about using a tool—it's about understanding it deeply enough to make it sing.",
            ],
            [
                'title' => 'The Art of Problem Solving',
                'slug' => 'the-art-of-problem-solving',
                'featured_image' => 'https://picsum.photos/seed/blog4/1200/630',
                'excerpt' => 'Breaking down complex problems into manageable pieces.',
                'content' => "Problem-solving in software development is less about knowing all the answers and more about knowing how to find them. It's about breaking down complex challenges into smaller, more manageable pieces.\n\nThe key is to resist the urge to solve everything at once. Instead, focus on understanding each component of the problem clearly. What looks like a single large problem is usually several smaller ones intertwined.\n\nThis approach—methodical, patient, and systematic—has helped me tackle increasingly complex projects. It's not about being the smartest person in the room; it's about being the most persistent.",
            ],
            [
                'title' => 'Continuous Learning',
                'slug' => 'continuous-learning',
                'featured_image' => 'https://picsum.photos/seed/blog5/1200/630',
                'excerpt' => 'How to stay current in an ever-evolving tech landscape.',
                'content' => "The tech landscape moves quickly, and staying current requires more than just reading documentation. It's about building a sustainable approach to learning that can keep pace with change without leading to burnout.\n\nI've found that the key is to focus on fundamentals while staying aware of trends. New frameworks and tools come and go, but solid principles of software design, clean code, and user experience endure.\n\nThis blog itself is part of my learning process—a way to solidify my understanding by explaining it to others. Because often, the best way to learn is to teach.",
            ],
        ];

        foreach ($posts as $postData) {
            $entry = Entry::query()->create([
                'title' => $postData['title'],
                'slug' => $postData['slug'],
                'blueprint_id' => $blueprint->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(random_int(1, 30)),
            ]);

            foreach ($blueprint->fields as $field) {
                EntryElement::query()->create([
                    'entry_id' => $entry->id,
                    'field_id' => $field->id,
                    'handle' => $field->handle,
                    'value' => $projectData[$field->handle] ?? '',
                ]);
            }
        }
    }

    private function createPortfolioItems(Blueprint $blueprint, User $admin): void
    {
        $projects = [
            [
                'title' => 'E-Commerce Platform Redesign',
                'slug' => 'ecommerce-platform-redesign',
                'project_image' => 'https://picsum.photos/seed/portfolio1/800/600',
                'description' => 'Redesigned a major e-commerce platform to improve user experience and increase conversion rates. Implemented modern design patterns and responsive layouts.',
                'technologies' => 'Laravel, Vue.js, Tailwind CSS, MySQL',
                'project_url' => 'https://example.com/project1',
                'github_url' => 'https://github.com/example/project1',
            ],
            [
                'title' => 'Mobile Banking Application',
                'slug' => 'mobile-banking-application',
                'project_image' => 'https://picsum.photos/seed/portfolio2/800/600',
                'description' => 'Developed a secure mobile banking application with biometric authentication and real-time transaction monitoring.',
                'technologies' => 'React Native, Node.js, PostgreSQL, AWS',
                'project_url' => 'https://example.com/project2',
                'github_url' => '',
            ],
            [
                'title' => 'Healthcare Management System',
                'slug' => 'healthcare-management-system',
                'project_image' => 'https://picsum.photos/seed/portfolio3/800/600',
                'description' => 'Built a comprehensive healthcare management system for patient records, appointment scheduling, and billing.',
                'technologies' => 'Laravel, Livewire, MySQL, Redis',
                'project_url' => 'https://example.com/project3',
                'github_url' => 'https://github.com/example/project3',
            ],
            [
                'title' => 'Real Estate Listing Platform',
                'slug' => 'real-estate-listing-platform',
                'project_image' => 'https://picsum.photos/seed/portfolio4/800/600',
                'description' => 'Created a feature-rich real estate platform with advanced search filters, virtual tours, and mortgage calculators.',
                'technologies' => 'Next.js, TypeScript, MongoDB, Stripe',
                'project_url' => 'https://example.com/project4',
                'github_url' => '',
            ],
            [
                'title' => 'Social Media Analytics Dashboard',
                'slug' => 'social-media-analytics-dashboard',
                'project_image' => 'https://picsum.photos/seed/portfolio5/800/600',
                'description' => 'Developed an analytics dashboard that provides insights from multiple social media platforms with real-time data visualization.',
                'technologies' => 'Python, Django, React, D3.js, PostgreSQL',
                'project_url' => 'https://example.com/project5',
                'github_url' => 'https://github.com/example/project5',
            ],
            [
                'title' => 'Event Management Platform',
                'slug' => 'event-management-platform',
                'project_image' => 'https://picsum.photos/seed/portfolio6/800/600',
                'description' => 'Built a complete event management system with ticketing, attendee management, and virtual event capabilities.',
                'technologies' => 'Laravel, Alpine.js, Tailwind CSS, Pusher',
                'project_url' => 'https://example.com/project6',
                'github_url' => '',
            ],
        ];

        foreach ($projects as $projectData) {
            $entry = Entry::query()->create([
                'title' => $projectData['title'],
                'slug' => $projectData['slug'],
                'blueprint_id' => $blueprint->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now(),
            ]);

            foreach ($blueprint->fields as $field) {
            EntryElement::query()->create([
                    'entry_id' => $entry->id,
                    'field_id' => $field->id,
                    'handle' => $field->handle,
                    'value' => $postData[$field->handle] ?? '',
                ]);
            }
        }
    }

    private function createContactPage(Blueprint $blueprint, User $admin): void
    {
        $entry = Entry::query()->create([
            'title' => 'Contact',
            'slug' => 'contact',
            'blueprint_id' => $blueprint->id,
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $fields = [
            'heading' => "Get in Touch",
            'subheading' => "Let's work together",
            'content' => "I'm always open to new ideas, collaborations, or a good conversation about code, design, or the process of building interesting things. Whether you have a project in mind or just want to connect, feel free to reach out.",
            'email' => 'contact@hector-moya.com',
            'linkedin_url' => 'https://www.linkedin.com/in/hector-moya-lopez/',
            'github_url' => 'https://github.com/hector-moya/',
        ];

        foreach ($blueprint->fields as $field) {
            EntryElement::query()->create([
                'entry_id' => $entry->id,
                'field_id' => $field->id,
                'handle' => $field->handle,
                'value' => $fields[$field->handle] ?? '',
            ]);
        }
    }
}
