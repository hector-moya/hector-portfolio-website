<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\UseSmartestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

#[UseSmartestModel]
final class EntryWizardAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
        You are a CMS content writer. Given a blueprint schema (list of section-type fields with their
        types and handles) and a topic brief, you generate appropriate content for each section field.

        Each field represents a page section. The field type is one of:
          hero, text, image_text, gallery, cta, features, richtext, form.

        For each field, return an object matching that section type's data shape:
          - hero: { title, subtitle, content, bg_image (null), cta_text, cta_url,
                    secondary_cta_text, secondary_cta_url }
          - text: { content, alignment ("left"|"center"|"right") }
          - image_text: { title, content, image (null), image_position ("left"|"right"),
                          cta_text, cta_url }
          - gallery: { title, images ([]) }
          - cta: { title, content, cta_text, cta_url }
          - features: { title, items ([{ icon, item_title, item_description }, ...]) }
          - richtext: { content (full HTML using <p>, <h2>, <ul>, <strong>, <em>) }
          - form: { title, fields ([]) }

        Rules:
        - Fill all text fields with realistic, relevant content for the given topic.
        - Set all image/asset fields (bg_image, image, images) to null or [].
        - For features items, include 3 items with a Heroicon name (e.g. "bolt", "star", "check").
        - For seo_title fields (type text): generate a concise, keyword-rich title under 60 characters.
        - For seo_description fields (type text): generate a compelling meta description under 160 characters.
        - Return a flat JSON object where each key is the field handle and the value is the section data object.
        INSTRUCTIONS;
    }

    /**
     * Empty object shape is intentional — field handles are runtime-dynamic (vary per blueprint).
     *
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->required(),
            'slug' => $schema->string()->required(),
            'fields' => $schema->object([])->required(),
        ];
    }
}
