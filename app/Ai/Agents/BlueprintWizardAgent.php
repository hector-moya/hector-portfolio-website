<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\UseSmartestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

#[UseSmartestModel]
final class BlueprintWizardAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
        You are a CMS blueprint architect. Given a description of a content type, you produce a
        structured blueprint definition with tabs, sections, and fields.

        Rules:
        - Always include a "Content" tab with the main content sections.
        - Always include an "SEO" tab with seo_title (text) and seo_description (text) fields.
        - Field handles must be snake_case, unique within the blueprint, and derived from the label.
        - Section handles must be snake_case derived from the section name.
        - Tab handles must be snake_case derived from the tab name.
        - Only use these field types: hero, text, image_text, gallery, cta, features, richtext, form.
          Each field represents a page section of that type.
        - Keep the structure practical: 2–4 tabs, 1–3 sections per tab, 3–8 fields per section.
        - Generate a name and URL-safe slug from the description.
        INSTRUCTIONS;
    }

    /**
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'name' => $schema->string()->required(),
            'slug' => $schema->string()->required(),
            'description' => $schema->string()->required(),
            'tabs' => $schema->array(
                $schema->object([
                    'name' => $schema->string()->required(),
                    'handle' => $schema->string()->required(),
                    'sections' => $schema->array(
                        $schema->object([
                            'name' => $schema->string()->required(),
                            'handle' => $schema->string()->required(),
                            'fields' => $schema->array(
                                $schema->object([
                                    'type' => $schema->string()->required(),
                                    'label' => $schema->string()->required(),
                                    'handle' => $schema->string()->required(),
                                    'instructions' => $schema->string(),
                                    'is_required' => $schema->boolean()->required(),
                                    'config' => $schema->string(),
                                ])
                            )->required(),
                        ])
                    )->required(),
                ])
            )->required(),
        ];
    }
}
