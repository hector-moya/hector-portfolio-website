<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\UseSmartestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

#[UseSmartestModel]
class EntryWizardAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
        You are a CMS content writer. Given a blueprint schema (list of fields with their types and handles)
        and a topic brief, you generate appropriate content values for each text-based field.

        Rules:
        - Fill text, textarea, email, url, number, date fields with real, relevant content.
        - For richtext fields, generate full HTML content (use <p>, <h2>, <ul>, <strong>, <em> tags).
        - For select and radio fields, pick the best option from the provided options array.
        - For toggle fields, return true or false as a boolean.
        - For image and file fields, return null.
        - For repeater fields, return null (scaffold handled separately).
        - For page_builder fields, return null.
        - For date fields, return today's date in Y-m-d format.
        - For seo_title: generate a concise, keyword-rich title under 60 characters.
        - For seo_description: generate a compelling meta description under 160 characters.
        - Return a flat JSON object where each key is the field handle and the value is the generated content.
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
