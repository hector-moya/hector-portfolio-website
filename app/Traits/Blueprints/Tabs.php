<?php

declare(strict_types=1);

namespace App\Traits\Blueprints;

use App\Enums\FieldType;
use App\Services\FieldTypeRegistry;
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
                                'label' => __('Title'),
                                'handle' => 'title',
                                'type' => 'text',
                                'icon' => FieldType::Text->icon(),
                                'sort_order' => 0,
                                'instructions' => __('The title of your content'),
                                'is_required' => true,
                                'config' => resolve(FieldTypeRegistry::class)->defaultConfigFor('text'),
                                'validation' => [],
                            ],
                            [
                                'id' => Uuid::uuid4()->toString(),
                                'label' => __('Content'),
                                'handle' => 'content',
                                'type' => 'richtext',
                                'icon' => FieldType::RichText->icon(),
                                'sort_order' => 1,
                                'instructions' => __('The main content body'),
                                'is_required' => true,
                                'config' => resolve(FieldTypeRegistry::class)->defaultConfigFor('rich_text'),
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
                                'label' => __('Excerpt'),
                                'handle' => 'excerpt',
                                'type' => 'textarea',
                                'icon' => FieldType::Textarea->icon(),
                                'sort_order' => 0,
                                'instructions' => __('A brief summary of the content'),
                                'is_required' => false,
                                'config' => resolve(FieldTypeRegistry::class)->defaultConfigFor('textarea'),
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
