<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Enums\SectionType;
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
        $sectionDocs = collect(SectionType::cases())
            ->map(function (SectionType $type): string {
                $fields = collect($type->fieldSchema())
                    ->map(function (array $f, string $handle): string {
                        $line = sprintf('    - %s (%s): %s', $handle, $f['type'], $f['label']);
                        if (isset($f['options'])) {
                            $line .= ' [options: '.implode(', ', array_keys($f['options'])).']';
                        }

                        return $line;
                    })
                    ->implode("\n");

                return "- **{$type->value}** ({$type->label()}):\n{$fields}";
            })
            ->implode("\n\n");

        return <<<INSTRUCTIONS
        You are a CMS content writer. Given a blueprint schema (list of section-type fields with their
        types and handles) and a topic brief, you generate appropriate content for each section field.

        Each field represents a page section. Available field types and their data shapes:

        {$sectionDocs}

        Rules:
        - Fill all text fields with realistic, relevant content for the given topic.
        - Set all image/asset fields (bg_image, image, images) to null or [].
        - For features items, include 3 items with a Heroicon name (e.g. "bolt", "star", "check").
        - For seo_title fields (type text_block): generate a concise, keyword-rich title under 60 characters.
        - For seo_description fields (type text_block): generate a compelling meta description under 160 characters.
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
