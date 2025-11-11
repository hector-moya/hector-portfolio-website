<?php

namespace App\Services;

use App\Enums\FieldType;
use App\Models\Section;
use Illuminate\Support\Str;

class BlueprintSectionPresets
{
    public static function addPreset(Section $section, string $preset): void
    {
        $method = Str::camel("add_{$preset}_fields");

        if (method_exists(static::class, $method)) {
            static::$method($section);
        }
    }

    protected static function addSeoFields(Section $section): void
    {
        $section->fields()->createMany([
            [
                'type' => FieldType::Text->value,
                'label' => 'Meta Title',
                'handle' => 'meta_title',
                'instructions' => 'The title that appears in search engine results',
                'required' => true,
                'sort_order' => 0,
            ],
            [
                'type' => FieldType::Textarea->value,
                'label' => 'Meta Description',
                'handle' => 'meta_description',
                'instructions' => 'A brief description for search engine results',
                'required' => true,
                'sort_order' => 1,
            ],
            [
                'type' => FieldType::Text->value,
                'label' => 'Canonical URL',
                'handle' => 'canonical_url',
                'instructions' => 'The preferred version of this page for search engines',
                'required' => false,
                'sort_order' => 2,
            ],
        ]);
    }

    protected static function addNavigationFields(Section $section): void
    {
        $section->fields()->createMany([
            [
                'type' => FieldType::Text->value,
                'label' => 'Link Text',
                'handle' => 'link_text',
                'instructions' => 'The text shown in the navigation',
                'required' => true,
                'sort_order' => 0,
            ],
            [
                'type' => FieldType::Text->value,
                'label' => 'URL',
                'handle' => 'url',
                'instructions' => 'The URL this item links to',
                'required' => true,
                'sort_order' => 1,
            ],
            [
                'type' => FieldType::Toggle->value,
                'label' => 'Open in New Window',
                'handle' => 'new_window',
                'instructions' => 'Should this link open in a new window?',
                'required' => false,
                'sort_order' => 2,
            ],
            [
                'type' => FieldType::Text->value,
                'label' => 'Icon Class',
                'handle' => 'icon_class',
                'instructions' => 'CSS class for the icon (optional)',
                'required' => false,
                'sort_order' => 3,
            ],
        ]);
    }

    protected static function addAssetFields(Section $section): void
    {
        $section->fields()->createMany([
            [
                'type' => FieldType::Text->value,
                'label' => 'Alt Text',
                'handle' => 'alt_text',
                'instructions' => 'Alternative text for screen readers',
                'required' => true,
                'sort_order' => 0,
            ],
            [
                'type' => FieldType::Text->value,
                'label' => 'Caption',
                'handle' => 'caption',
                'instructions' => 'A caption to display with the asset',
                'required' => false,
                'sort_order' => 1,
            ],
            [
                'type' => FieldType::Text->value,
                'label' => 'Copyright',
                'handle' => 'copyright',
                'instructions' => 'Copyright information',
                'required' => false,
                'sort_order' => 2,
            ],
            [
                'type' => FieldType::Text->value,
                'label' => 'Credit',
                'handle' => 'credit',
                'instructions' => 'Credit for the asset (photographer, artist, etc.)',
                'required' => false,
                'sort_order' => 3,
            ],
        ]);
    }

    protected static function addUserProfileFields(Section $section): void
    {
        $section->fields()->createMany([
            [
                'type' => FieldType::Text->value,
                'label' => 'Display Name',
                'handle' => 'display_name',
                'instructions' => 'The name to display publicly',
                'required' => true,
                'sort_order' => 0,
            ],
            [
                'type' => FieldType::Textarea->value,
                'label' => 'Bio',
                'handle' => 'bio',
                'instructions' => 'A brief biography',
                'required' => false,
                'sort_order' => 1,
            ],
            [
                'type' => FieldType::Text->value,
                'label' => 'Twitter Handle',
                'handle' => 'twitter',
                'instructions' => 'Your Twitter username',
                'required' => false,
                'sort_order' => 2,
            ],
            [
                'type' => FieldType::Text->value,
                'label' => 'Website',
                'handle' => 'website',
                'instructions' => 'Your personal website',
                'required' => false,
                'sort_order' => 3,
            ],
        ]);
    }
}
