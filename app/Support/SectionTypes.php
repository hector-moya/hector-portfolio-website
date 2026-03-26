<?php

namespace App\Support;

class SectionTypes
{
    /**
     * @return array<string, array{label: string, fields: array<string, array{type: string, label: string, default: mixed, options?: array<string, string>}>}>
     */
    public static function all(): array
    {
        return [
            'hero' => [
                'label' => 'Hero',
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                    'subtitle' => ['type' => 'text', 'label' => 'Subtitle', 'default' => ''],
                    'content' => ['type' => 'textarea', 'label' => 'Content', 'default' => ''],
                    'bg_image' => ['type' => 'image', 'label' => 'Background Image', 'default' => null],
                    'cta_text' => ['type' => 'text', 'label' => 'Primary CTA Text', 'default' => ''],
                    'cta_url' => ['type' => 'text', 'label' => 'Primary CTA URL', 'default' => ''],
                    'secondary_cta_text' => ['type' => 'text', 'label' => 'Secondary CTA Text', 'default' => ''],
                    'secondary_cta_url' => ['type' => 'text', 'label' => 'Secondary CTA URL', 'default' => ''],
                ],
            ],
            'text' => [
                'label' => 'Text',
                'fields' => [
                    'content' => ['type' => 'textarea', 'label' => 'Content', 'default' => ''],
                    'alignment' => [
                        'type' => 'select',
                        'label' => 'Alignment',
                        'default' => 'left',
                        'options' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right'],
                    ],
                ],
            ],
            'image_text' => [
                'label' => 'Image + Text',
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                    'content' => ['type' => 'textarea', 'label' => 'Content', 'default' => ''],
                    'image' => ['type' => 'image', 'label' => 'Image', 'default' => null],
                    'image_position' => [
                        'type' => 'select',
                        'label' => 'Image Position',
                        'default' => 'left',
                        'options' => ['left' => 'Left', 'right' => 'Right'],
                    ],
                    'cta_text' => ['type' => 'text', 'label' => 'CTA Text', 'default' => ''],
                    'cta_url' => ['type' => 'text', 'label' => 'CTA URL', 'default' => ''],
                ],
            ],
            'gallery' => [
                'label' => 'Gallery',
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                    'images' => ['type' => 'asset_list', 'label' => 'Images', 'default' => []],
                ],
            ],
            'cta' => [
                'label' => 'Call to Action',
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                    'content' => ['type' => 'textarea', 'label' => 'Content', 'default' => ''],
                    'cta_text' => ['type' => 'text', 'label' => 'CTA Text', 'default' => ''],
                    'cta_url' => ['type' => 'text', 'label' => 'CTA URL', 'default' => ''],
                ],
            ],
            'features' => [
                'label' => 'Features',
                'fields' => [
                    'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                    'items' => ['type' => 'repeater', 'label' => 'Features', 'default' => []],
                ],
            ],
        ];
    }

    /**
     * @return array{label: string, fields: array<string, array{type: string, label: string, default: mixed, options?: array<string, string>}>}|null
     */
    public static function get(string $type): ?array
    {
        return static::all()[$type] ?? null;
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(string $type): array
    {
        $schema = static::get($type);

        if ($schema === null) {
            return [];
        }

        return array_map(fn (array $field) => $field['default'], $schema['fields']);
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return array_map(fn (array $type) => $type['label'], static::all());
    }
}
