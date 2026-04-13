<?php

declare(strict_types=1);

namespace App\Traits\Blueprints;

use App\Enums\SectionType;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

trait Tabs
{
    public function initializeTabs(): array
    {
        return [
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => __('Main'),
                'handle' => 'main',
                'sort_order' => 0,
                'sections' => [
                    [
                        'id' => Uuid::uuid4()->toString(),
                        'name' => __('Content'),
                        'handle' => 'content',
                        'sort_order' => 0,
                        'instructions' => '',
                        'fields' => [
                            [
                                'id' => Uuid::uuid4()->toString(),
                                'label' => __('Hero'),
                                'handle' => 'hero',
                                'type' => SectionType::Hero->value,
                                'icon' => SectionType::Hero->icon(),
                                'sort_order' => 0,
                                'instructions' => __('The hero section of your page'),
                                'is_required' => false,
                                'config' => SectionType::Hero->defaultConfig(),
                                'validation' => [],
                            ],
                            [
                                'id' => Uuid::uuid4()->toString(),
                                'label' => __('Content'),
                                'handle' => 'content',
                                'type' => SectionType::RichText->value,
                                'icon' => SectionType::RichText->icon(),
                                'sort_order' => 1,
                                'instructions' => __('The main content body'),
                                'is_required' => false,
                                'config' => SectionType::RichText->defaultConfig(),
                                'validation' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'name' => __('Side'),
                'handle' => 'side',
                'sort_order' => 1,
                'sections' => [
                    [
                        'id' => Uuid::uuid4()->toString(),
                        'name' => __('Meta'),
                        'handle' => 'meta',
                        'sort_order' => 0,
                        'instructions' => '',
                        'fields' => [
                            [
                                'id' => Uuid::uuid4()->toString(),
                                'label' => __('Text'),
                                'handle' => 'text',
                                'type' => SectionType::Text->value,
                                'icon' => SectionType::Text->icon(),
                                'sort_order' => 0,
                                'instructions' => __('A text content section'),
                                'is_required' => false,
                                'config' => SectionType::Text->defaultConfig(),
                                'validation' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public function createTab(string $name): array
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'name' => $name,
            'handle' => Str::slug($name),
            'sort_order' => 0,
            'sections' => [[
                'id' => Uuid::uuid4()->toString(),
                'name' => __('New Section'),
                'handle' => Str::slug(__('New Section')),
                'sort_order' => 0,
                'instructions' => '',
                'fields' => [],
            ], ],
        ];
    }
}
