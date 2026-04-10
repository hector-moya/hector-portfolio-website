<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Support\SectionTypes;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\UseSmartestModel;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

#[UseSmartestModel]
final class LandingPageWizardAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
    {
        $sectionDocs = collect(SectionTypes::all())
            ->map(function (array $type, string $key): string {
                $fields = collect($type['fields'])
                    ->map(function (array $f, string $handle): string {
                        $line = sprintf('    - %s (%s): %s', $handle, $f['type'], $f['label']);
                        if (isset($f['options'])) {
                            $line .= ' [options: '.implode(', ', array_keys($f['options'])).']';
                        }

                        return $line;
                    })
                    ->implode("\n");

                return "- **{$key}** ({$type['label']}):\n{$fields}";
            })
            ->implode("\n\n");

        return <<<INSTRUCTIONS
        You are a landing page content architect. Given a description of a landing page, produce an
        ordered list of page builder sections with pre-filled content.

        Available section types and their fields:

        {$sectionDocs}

        Rules:
        - Always start with a "hero" section.
        - Include 3–6 sections total. Choose types that make sense for the described page.
        - Fill all text fields with realistic, relevant content.
        - Set all fields of type `image` or `asset_list` to null or [] — images are added manually later.
        - In `data`, only populate the fields that belong to the section's declared type. Do not fill keys from other section types.
        - For "features" sections, include 3 feature items in the items array, each with icon (a
          Heroicon name like "bolt", "star", "check"), item_title, and item_description.
        - Each section must have a unique "_id" — use a short random-looking alphanumeric string
          (e.g., "a1b2c3").
        - Do not include page_builder, repeater, calendar, or time sections — these are field types,
          not section types.
        INSTRUCTIONS;
    }

    /**
     * Empty items array shape is intentional — feature items are runtime-dynamic.
     *
     * @return array<string, mixed>
     */
    public function schema(JsonSchema $schema): array
    {
        $dataShape = $schema->object([
            'title' => $schema->string(),
            'subtitle' => $schema->string(),
            'content' => $schema->string(),
            'cta_text' => $schema->string(),
            'cta_url' => $schema->string(),
            'secondary_cta_text' => $schema->string(),
            'secondary_cta_url' => $schema->string(),
            'image_position' => $schema->string(),
            'alignment' => $schema->string(),
            'bg_image' => $schema->string()->nullable(),
            'image' => $schema->string()->nullable(),
            'images' => $schema->array($schema->string()->nullable()),
            'items' => $schema->array(
                $schema->object([
                    'icon' => $schema->string()->required(),
                    'item_title' => $schema->string()->required(),
                    'item_description' => $schema->string()->required(),
                ])
            ),
        ]);

        return [
            'sections' => $schema->array(
                $schema->object([
                    '_id' => $schema->string()->required(),
                    'type' => $schema->string()->required(),
                    'data' => $dataShape->required(),
                ])
            )->required(),
        ];
    }
}
