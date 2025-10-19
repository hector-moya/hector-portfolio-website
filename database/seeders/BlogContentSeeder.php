<?php

namespace Database\Seeders;

use App\Models\Blueprint;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create admin user
        $admin = \App\Models\User::query()->where('role', 'admin')->first();

        // Create Blueprints
        $pageBlueprint = $this->createHomePageBlueprint();
        $blogBlueprint = $this->createBlogBlueprint();
        $portfolioBlueprint = $this->createPortfolioBlueprint();
        $contactBlueprint = $this->createContactBlueprint();

        // Create Collections
        $pagesCollection = \App\Models\Collection::query()->create([
            'name' => 'Pages',
            'slug' => 'pages',
            'description' => 'Static pages for the website',
            'blueprint_id' => $pageBlueprint->id,
        ]);

        $blogCollection = \App\Models\Collection::query()->create([
            'name' => 'Blog',
            'slug' => 'blog',
            'description' => 'Blog posts and articles',
            'blueprint_id' => $blogBlueprint->id,
        ]);

        $portfolioCollection = \App\Models\Collection::query()->create([
            'name' => 'Portfolio',
            'slug' => 'portfolio',
            'description' => 'Portfolio projects and work samples',
            'blueprint_id' => $portfolioBlueprint->id,
        ]);

        $contactCollection = \App\Models\Collection::query()->create([
            'name' => 'Contact',
            'slug' => 'contact',
            'description' => 'Contact page content',
            'blueprint_id' => $contactBlueprint->id,
        ]);

        // Create Entries
        $this->createLandingPage($pagesCollection, $pageBlueprint, $admin);
        $this->createBlogPosts($blogCollection, $blogBlueprint, $admin);
        $this->createPortfolioItems($portfolioCollection, $portfolioBlueprint, $admin);
        $this->createContactPage($contactCollection, $contactBlueprint, $admin);

        $this->command->info('Blog content seeded successfully!');
    }

    private function createHomePageBlueprint(): Blueprint
    {
        $blueprint = \App\Models\Blueprint::query()->create([
            'name' => 'Home Page',
            'slug' => 'home-page',
            'description' => 'Home page template',
        ]);

        $elements = [
            ['label' => 'Hero Title', 'handle' => 'hero_title', 'type' => 'text', 'is_required' => true, 'order' => 1],
            ['label' => 'Hero Subtitle', 'handle' => 'hero_subtitle', 'type' => 'text', 'is_required' => false, 'order' => 2],
            ['label' => 'Hero Image', 'handle' => 'hero_image', 'type' => 'url', 'is_required' => false, 'order' => 3],
            ['label' => 'Content', 'handle' => 'content', 'type' => 'textarea', 'is_required' => true, 'order' => 4],
            ['label' => 'Call to Action Primary Text', 'handle' => 'cta_text_primary', 'type' => 'text', 'is_required' => false, 'order' => 5],
            ['label' => 'Call to Action Primary URL', 'handle' => 'cta_url_primary', 'type' => 'url', 'is_required' => false, 'order' => 6],
            ['label' => 'Call to Action Secondary Text', 'handle' => 'cta_text_secondary', 'type' => 'text', 'is_required' => false, 'order' => 7],
            ['label' => 'Call to Action Secondary URL', 'handle' => 'cta_url_secondary', 'type' => 'url', 'is_required' => false, 'order' => 8],
        ];

        foreach ($elements as $element) {
            \App\Models\BlueprintElement::query()->create(array_merge(['blueprint_id' => $blueprint->id], $element));
        }

        return $blueprint;
    }

    private function createBlogBlueprint(): Blueprint
    {
        $blueprint = \App\Models\Blueprint::query()->create([
            'name' => 'Blog Post',
            'slug' => 'blog-post',
            'description' => 'Blog post template',
        ]);

        $elements = [
            ['label' => 'Featured Image', 'handle' => 'featured_image', 'type' => 'url', 'is_required' => false, 'order' => 1],
            ['label' => 'Excerpt', 'handle' => 'excerpt', 'type' => 'textarea', 'is_required' => true, 'order' => 2],
            ['label' => 'Content', 'handle' => 'content', 'type' => 'textarea', 'is_required' => true, 'order' => 3],
            ['label' => 'Category', 'handle' => 'category', 'type' => 'text', 'is_required' => false, 'order' => 4],
            ['label' => 'Tags', 'handle' => 'tags', 'type' => 'text', 'is_required' => false, 'order' => 5],
            ['label' => 'Reading Time (minutes)', 'handle' => 'reading_time', 'type' => 'number', 'is_required' => false, 'order' => 6],
        ];

        foreach ($elements as $element) {
            \App\Models\BlueprintElement::query()->create(array_merge(['blueprint_id' => $blueprint->id], $element));
        }

        return $blueprint;
    }

    private function createPortfolioBlueprint(): Blueprint
    {
        $blueprint = \App\Models\Blueprint::query()->create([
            'name' => 'Portfolio Item',
            'slug' => 'portfolio-item',
            'description' => 'Portfolio project template',
        ]);

        $elements = [
            ['label' => 'Project Image', 'handle' => 'project_image', 'type' => 'url', 'is_required' => true, 'order' => 1],
            ['label' => 'Description', 'handle' => 'description', 'type' => 'textarea', 'is_required' => true, 'order' => 2],
            ['label' => 'Client', 'handle' => 'client', 'type' => 'text', 'is_required' => false, 'order' => 3],
            ['label' => 'Project Date', 'handle' => 'project_date', 'type' => 'date', 'is_required' => false, 'order' => 4],
            ['label' => 'Technologies', 'handle' => 'technologies', 'type' => 'text', 'is_required' => false, 'order' => 5],
            ['label' => 'Project URL', 'handle' => 'project_url', 'type' => 'url', 'is_required' => false, 'order' => 6],
            ['label' => 'GitHub URL', 'handle' => 'github_url', 'type' => 'url', 'is_required' => false, 'order' => 7],
        ];

        foreach ($elements as $element) {
            \App\Models\BlueprintElement::query()->create(array_merge(['blueprint_id' => $blueprint->id], $element));
        }

        return $blueprint;
    }

    private function createContactBlueprint(): Blueprint
    {
        $blueprint = \App\Models\Blueprint::query()->create([
            'name' => 'Contact Page',
            'slug' => 'contact-page',
            'description' => 'Contact page template',
        ]);

        $elements = [
            ['label' => 'Heading', 'handle' => 'heading', 'type' => 'text', 'is_required' => true, 'order' => 1],
            ['label' => 'Description', 'handle' => 'description', 'type' => 'textarea', 'is_required' => true, 'order' => 2],
            ['label' => 'Email', 'handle' => 'email', 'type' => 'email', 'is_required' => true, 'order' => 3],
            ['label' => 'LinkedIn', 'handle' => 'linkedin', 'type' => 'url', 'is_required' => false, 'order' => 4],
            ['label' => 'GitHub', 'handle' => 'github', 'type' => 'url', 'is_required' => false, 'order' => 5],
        ];

        foreach ($elements as $element) {
            \App\Models\BlueprintElement::query()->create(array_merge(['blueprint_id' => $blueprint->id], $element));
        }

        return $blueprint;
    }

    private function createLandingPage(Collection $collection, Blueprint $blueprint, User $admin): void
    {
        $entry = \App\Models\Entry::query()->create([
            'title' => 'Home - Landing Page',
            'slug' => 'home',
            'collection_id' => $collection->id,
            'blueprint_id' => $blueprint->id,
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $elements = [
            'hero_title' => 'Progress, not perfection. The work is never done. That’s the point.',
            'hero_subtitle' => 'A living archive of the things I build, learn, and break.',
            'hero_image' => 'https://picsum.photos/seed/hero/1920/1080',
            'content' => 'The work of building software is rarely elegant. It’s an ongoing loop of trial, adjustment, and acceptance — an exercise in problem-solving where every solution creates new questions.
                           I’ve stopped expecting the process to end cleanly; it never does. And that’s what keeps it interesting.
                           My approach is pragmatic but curious. I prefer structure to style, clarity to perfection. I believe that most good systems are the result of patience — small, deliberate refinements layered over time.
                           Sometimes the best thing I can do is step back, re-evaluate, and start again with a better question.
                           This site is where that loop continues — a place to record what I’ve built, what I’ve learned, and the patterns I keep chasing. The goal isn’t to impress; it’s to understand.',
            'cta_text_primary' => 'See what I’m building',
            'cta_url_primary' => '/portfolio',
            'cta_text_secondary' => 'Check the journey',
            'cta_url_secondary' => '/blog',
        ];

        foreach ($blueprint->elements as $blueprintElement) {
            \App\Models\EntryElement::query()->create([
                'entry_id' => $entry->id,
                'blueprint_element_id' => $blueprintElement->id,
                'handle' => $blueprintElement->handle,
                'value' => $elements[$blueprintElement->handle] ?? '',
            ]);
        }
    }

    private function createBlogPosts(Collection $collection, Blueprint $blueprint, User $admin): void
    {
        $posts = [
            [
                'title' => 'Getting Started with Modern Web Development',
                'slug' => 'getting-started-modern-web-development',
                'featured_image' => 'https://picsum.photos/seed/blog1/1200/630',
                'excerpt' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Discover the essential tools and frameworks every developer should know.',
                'content' => "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\n\nDuis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n\nSed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit.",
                'category' => 'Development',
                'tags' => 'web development, programming, tutorial',
                'reading_time' => '5',
            ],
            [
                'title' => 'The Future of Artificial Intelligence',
                'slug' => 'future-of-artificial-intelligence',
                'featured_image' => 'https://picsum.photos/seed/blog2/1200/630',
                'excerpt' => 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque.',
                'content' => "At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident.\n\nSimilique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus.\n\nOmnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae.",
                'category' => 'Technology',
                'tags' => 'AI, machine learning, future tech',
                'reading_time' => '8',
            ],
            [
                'title' => 'Design Principles for Better User Experience',
                'slug' => 'design-principles-better-ux',
                'featured_image' => 'https://picsum.photos/seed/blog3/1200/630',
                'excerpt' => 'Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur.',
                'content' => "Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur.\n\nAt vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi.\n\nId est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est.",
                'category' => 'Design',
                'tags' => 'UX, design, user interface',
                'reading_time' => '6',
            ],
            [
                'title' => 'Building Scalable Applications with Laravel',
                'slug' => 'building-scalable-applications-laravel',
                'featured_image' => 'https://picsum.photos/seed/blog4/1200/630',
                'excerpt' => 'Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates.',
                'content' => "Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus.\n\nUt aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.\n\nUt enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.",
                'category' => 'Development',
                'tags' => 'Laravel, PHP, backend',
                'reading_time' => '10',
            ],
            [
                'title' => 'Mobile-First Design Strategies',
                'slug' => 'mobile-first-design-strategies',
                'featured_image' => 'https://picsum.photos/seed/blog5/1200/630',
                'excerpt' => 'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur adipisci velit.',
                'content' => "Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.\n\nUt enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur. Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur.\n\nVel illum qui dolorem eum fugiat quo voluptas nulla pariatur. At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti.",
                'category' => 'Design',
                'tags' => 'mobile, responsive design, strategy',
                'reading_time' => '7',
            ],
        ];

        foreach ($posts as $postData) {
            $entry = \App\Models\Entry::query()->create([
                'title' => $postData['title'],
                'slug' => $postData['slug'],
                'collection_id' => $collection->id,
                'blueprint_id' => $blueprint->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now()->subDays(random_int(1, 30)),
            ]);

            foreach ($blueprint->elements as $blueprintElement) {
                \App\Models\EntryElement::query()->create([
                    'entry_id' => $entry->id,
                    'blueprint_element_id' => $blueprintElement->id,
                    'handle' => $blueprintElement->handle,
                    'value' => $postData[$blueprintElement->handle] ?? '',
                ]);
            }
        }
    }

    private function createPortfolioItems(Collection $collection, Blueprint $blueprint, User $admin): void
    {
        $projects = [
            [
                'title' => 'E-Commerce Platform Redesign',
                'slug' => 'ecommerce-platform-redesign',
                'project_image' => 'https://picsum.photos/seed/portfolio1/800/600',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Redesigned a major e-commerce platform to improve user experience and increase conversion rates. Implemented modern design patterns and responsive layouts.',
                'client' => 'TechShop Inc.',
                'project_date' => '2024-08-15',
                'technologies' => 'Laravel, Vue.js, Tailwind CSS, MySQL',
                'project_url' => 'https://example.com/project1',
                'github_url' => 'https://github.com/example/project1',
            ],
            [
                'title' => 'Mobile Banking Application',
                'slug' => 'mobile-banking-application',
                'project_image' => 'https://picsum.photos/seed/portfolio2/800/600',
                'description' => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Developed a secure mobile banking application with biometric authentication and real-time transaction monitoring.',
                'client' => 'FinanceBank',
                'project_date' => '2024-06-20',
                'technologies' => 'React Native, Node.js, PostgreSQL, AWS',
                'project_url' => 'https://example.com/project2',
                'github_url' => '',
            ],
            [
                'title' => 'Healthcare Management System',
                'slug' => 'healthcare-management-system',
                'project_image' => 'https://picsum.photos/seed/portfolio3/800/600',
                'description' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco. Built a comprehensive healthcare management system for patient records, appointment scheduling, and billing.',
                'client' => 'MedClinic',
                'project_date' => '2024-04-10',
                'technologies' => 'Laravel, Livewire, MySQL, Redis',
                'project_url' => 'https://example.com/project3',
                'github_url' => 'https://github.com/example/project3',
            ],
            [
                'title' => 'Real Estate Listing Platform',
                'slug' => 'real-estate-listing-platform',
                'project_image' => 'https://picsum.photos/seed/portfolio4/800/600',
                'description' => 'Duis aute irure dolor in reprehenderit in voluptate velit. Created a feature-rich real estate platform with advanced search filters, virtual tours, and mortgage calculators.',
                'client' => 'PropertyPro',
                'project_date' => '2024-02-28',
                'technologies' => 'Next.js, TypeScript, MongoDB, Stripe',
                'project_url' => 'https://example.com/project4',
                'github_url' => '',
            ],
            [
                'title' => 'Social Media Analytics Dashboard',
                'slug' => 'social-media-analytics-dashboard',
                'project_image' => 'https://picsum.photos/seed/portfolio5/800/600',
                'description' => 'Excepteur sint occaecat cupidatat non proident. Developed an analytics dashboard that provides insights from multiple social media platforms with real-time data visualization.',
                'client' => 'SocialMetrics',
                'project_date' => '2023-12-15',
                'technologies' => 'Python, Django, React, D3.js, PostgreSQL',
                'project_url' => 'https://example.com/project5',
                'github_url' => 'https://github.com/example/project5',
            ],
            [
                'title' => 'Event Management Platform',
                'slug' => 'event-management-platform',
                'project_image' => 'https://picsum.photos/seed/portfolio6/800/600',
                'description' => 'Sunt in culpa qui officia deserunt mollit anim id est laborum. Built a complete event management system with ticketing, attendee management, and virtual event capabilities.',
                'client' => 'EventHub',
                'project_date' => '2023-10-05',
                'technologies' => 'Laravel, Alpine.js, Tailwind CSS, Pusher',
                'project_url' => 'https://example.com/project6',
                'github_url' => '',
            ],
        ];

        foreach ($projects as $projectData) {
            $entry = \App\Models\Entry::query()->create([
                'title' => $projectData['title'],
                'slug' => $projectData['slug'],
                'collection_id' => $collection->id,
                'blueprint_id' => $blueprint->id,
                'author_id' => $admin->id,
                'status' => 'published',
                'published_at' => now(),
            ]);

            foreach ($blueprint->elements as $blueprintElement) {
                \App\Models\EntryElement::query()->create([
                    'entry_id' => $entry->id,
                    'blueprint_element_id' => $blueprintElement->id,
                    'handle' => $blueprintElement->handle,
                    'value' => $projectData[$blueprintElement->handle] ?? '',
                ]);
            }
        }
    }

    private function createContactPage(Collection $collection, Blueprint $blueprint, User $admin): void
    {
        $entry = \App\Models\Entry::query()->create([
            'title' => 'Contact',
            'slug' => 'contact',
            'collection_id' => $collection->id,
            'blueprint_id' => $blueprint->id,
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        $elements = [
            'heading' => 'Get in Touch',
            'description' => 'I’m always open to new ideas, collaborations, or a good conversation about code, design, or the process of building interesting things.
                              Whether you’re looking to work together, have a question about one of my projects, or just want to exchange thoughts on web development,
                              feel free to reach out. You can contact me using the form below — I’ll do my best to reply soon.',
            'email' => 'contact@hector-moya.com',
            'linkedin' => 'https://www.linkedin.com/in/hector-moya-lopez/',
            'github' => 'https://github.com/hector-moya/',
        ];

        foreach ($blueprint->elements as $blueprintElement) {
            \App\Models\EntryElement::query()->create([
                'entry_id' => $entry->id,
                'blueprint_element_id' => $blueprintElement->id,
                'handle' => $blueprintElement->handle,
                'value' => $elements[$blueprintElement->handle] ?? '',
            ]);
        }
    }
}
