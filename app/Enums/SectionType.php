<?php

declare(strict_types=1);

namespace App\Enums;

enum SectionType: string
{
    case Hero = 'hero';
    case Text = 'text_block';
    case ImageText = 'image_text';
    case Gallery = 'gallery';
    case Cta = 'cta';
    case Features = 'features';
    case RichText = 'richtext_block';
    case Form = 'form';
    case CardGrid = 'card_grid';

    public function label(): string
    {
        return match ($this) {
            self::Hero => 'Hero',
            self::Text => 'Text',
            self::ImageText => 'Image + Text',
            self::Gallery => 'Gallery',
            self::Cta => 'Call to Action',
            self::Features => 'Features',
            self::RichText => 'Rich Text',
            self::Form => 'Form',
            self::CardGrid => 'Card Grid',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Hero => 'sparkles',
            self::Text => 'document-text',
            self::ImageText => 'photo',
            self::Gallery => 'squares-2x2',
            self::Cta => 'cursor-arrow-rays',
            self::Features => 'rectangle-group',
            self::RichText => 'book-open',
            self::Form => 'clipboard-document-list',
            self::CardGrid => 'rectangle-stack',
        };
    }

    public function defaultLabel(): string
    {
        return match ($this) {
            self::Hero => 'Hero Section',
            self::Text => 'Text Section',
            self::ImageText => 'Image + Text Section',
            self::Gallery => 'Gallery Section',
            self::Cta => 'Call to Action Section',
            self::Features => 'Features Section',
            self::RichText => 'Rich Text Section',
            self::Form => 'Form Section',
            self::CardGrid => 'Card Grid Section',
        };
    }

    /**
     * Default data structure for a new instance of this section type.
     *
     * @return array<string, mixed>
     */
    public function defaultData(): array
    {
        return match ($this) {
            self::Hero => [
                'title' => '',
                'subtitle' => '',
                'content' => '',
                'bg_image' => null,
                'cta_text' => '',
                'cta_url' => '',
                'secondary_cta_text' => '',
                'secondary_cta_url' => '',
            ],
            self::Text => [
                'content' => '',
                'alignment' => 'left',
            ],
            self::ImageText => [
                'title' => '',
                'content' => '',
                'image' => null,
                'image_position' => 'left',
                'cta_text' => '',
                'cta_url' => '',
            ],
            self::Gallery => [
                'title' => '',
                'images' => [],
            ],
            self::Cta => [
                'title' => '',
                'content' => '',
                'cta_text' => '',
                'cta_url' => '',
            ],
            self::Features => [
                'title' => '',
                'items' => [],
            ],
            self::RichText => [
                'content' => '',
            ],
            self::Form => [
                'title' => '',
                'fields' => [],
            ],
            self::CardGrid => [
                'title' => '',
                'subtitle' => '',
                'cards' => [],
            ],
        };
    }

    /**
     * Schema describing the fields this section type contains.
     *
     * @return array<string, array{type: string, label: string, default: mixed, options?: array<string, string>}>
     */
    public function fieldSchema(): array
    {
        return match ($this) {
            self::Hero => [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                'subtitle' => ['type' => 'text', 'label' => 'Subtitle', 'default' => ''],
                'content' => ['type' => 'textarea', 'label' => 'Content', 'default' => ''],
                'bg_image' => ['type' => 'image', 'label' => 'Background Image', 'default' => null],
                'cta_text' => ['type' => 'text', 'label' => 'Primary CTA Text', 'default' => ''],
                'cta_url' => ['type' => 'text', 'label' => 'Primary CTA URL', 'default' => ''],
                'secondary_cta_text' => ['type' => 'text', 'label' => 'Secondary CTA Text', 'default' => ''],
                'secondary_cta_url' => ['type' => 'text', 'label' => 'Secondary CTA URL', 'default' => ''],
            ],
            self::Text => [
                'content' => ['type' => 'textarea', 'label' => 'Content', 'default' => ''],
                'alignment' => [
                    'type' => 'select',
                    'label' => 'Alignment',
                    'default' => 'left',
                    'options' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right'],
                ],
            ],
            self::ImageText => [
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
            self::Gallery => [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                'images' => ['type' => 'asset_list', 'label' => 'Images', 'default' => []],
            ],
            self::Cta => [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                'content' => ['type' => 'textarea', 'label' => 'Content', 'default' => ''],
                'cta_text' => ['type' => 'text', 'label' => 'CTA Text', 'default' => ''],
                'cta_url' => ['type' => 'text', 'label' => 'CTA URL', 'default' => ''],
            ],
            self::Features => [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                'items' => ['type' => 'repeater', 'label' => 'Features', 'default' => []],
            ],
            self::RichText => [
                'content' => ['type' => 'richtext', 'label' => 'Content', 'default' => ''],
            ],
            self::Form => [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                'fields' => ['type' => 'form_fields', 'label' => 'Form Fields', 'default' => []],
            ],
            self::CardGrid => [
                'title' => ['type' => 'text', 'label' => 'Title', 'default' => ''],
                'subtitle' => ['type' => 'text', 'label' => 'Subtitle', 'default' => ''],
                'cards' => ['type' => 'repeater', 'label' => 'Cards', 'default' => []],
            ],
        };
    }

    /**
     * Default configuration for this section type (used in blueprint field config).
     *
     * @return array<string, mixed>
     */
    public function defaultConfig(): array
    {
        return match ($this) {
            self::Hero => [],
            self::Text => [],
            self::ImageText => [],
            self::Gallery => [
                'max_images' => 6,
            ],
            self::Cta => [],
            self::Features => [
                'max_items' => null,
            ],
            self::RichText => [
                'toolbar' => [
                    'heading',
                    'bold',
                    'italic',
                    'strike',
                    'underline',
                    'bullet',
                    'ordered',
                    'blockquote',
                    'link',
                    'code',
                    'undo',
                    'redo',
                ],
            ],
            self::Form => [
                'available_field_types' => ['text', 'textarea', 'email', 'number', 'date', 'time', 'toggle', 'select', 'radio'],
            ],
            self::CardGrid => [
                'columns' => 3,
                'max_items' => null,
            ],
        };
    }

    /**
     * Validation rules for section configuration (used when saving blueprint).
     *
     * @return array<string, array<int, string>>
     */
    public function configRules(): array
    {
        return match ($this) {
            self::Hero, self::Text, self::ImageText, self::Cta => [],
            self::CardGrid => [
                'columns' => ['nullable', 'integer', 'in:2,3,4'],
                'max_items' => ['nullable', 'integer', 'min:1'],
            ],
            self::Gallery => [
                'max_images' => ['nullable', 'integer', 'min:1', 'max:20'],
            ],
            self::Features => [
                'max_items' => ['nullable', 'integer', 'min:1'],
            ],
            self::RichText => [
                'toolbar' => ['array'],
                'toolbar.*' => ['string'],
            ],
            self::Form => [
                'available_field_types' => ['array'],
                'available_field_types.*' => ['string'],
            ],
        };
    }
}
