<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migrate blueprint fields from old FieldType values to SectionType values.
     * Also migrate entry_elements and legacy entries.layout data.
     */
    public function up(): void
    {
        // 1. Map old field types to new section types in the fields table
        $typeMapping = [
            'textarea' => 'text',       // textarea → text section
            'url' => 'text',            // url → text section
            'email' => 'form',          // email → form section
            'image' => 'image_text',    // image → image_text section
            'repeater' => 'features',   // repeater → features section
            // 'text' stays as 'text'
            // 'richtext' stays as 'richtext'
            // 'page_builder' will be handled separately
        ];

        foreach ($typeMapping as $oldType => $newType) {
            DB::table('fields')
                ->where('type', $oldType)
                ->update(['type' => $newType]);
        }

        // 2. Handle page_builder fields: convert to richtext (the page builder
        // sections are stored in entry_elements and will be handled by
        // getPageBuilderSections() legacy support)
        DB::table('fields')
            ->where('type', 'page_builder')
            ->update(['type' => 'richtext']);

        // 3. Migrate legacy entries.layout column data to entry_elements
        $entriesWithLayout = DB::table('entries')
            ->whereNotNull('layout')
            ->get(['id', 'layout', 'blueprint_id']);

        foreach ($entriesWithLayout as $entry) {
            $layout = json_decode((string) $entry->layout, true);
            if (! is_array($layout)) {
                continue;
            }
            if ($layout === []) {
                continue;
            }

            // Find or create a field for the page builder sections
            $field = DB::table('fields')
                ->where('blueprint_id', $entry->blueprint_id)
                ->where('handle', 'page_layout')
                ->first();

            if (! $field) {
                $fieldId = DB::table('fields')->insertGetId([
                    'blueprint_id' => $entry->blueprint_id,
                    'type' => 'richtext',
                    'label' => 'Page Layout',
                    'handle' => 'page_layout',
                    'instructions' => '',
                    'is_required' => false,
                    'config' => json_encode([]),
                    'order' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $fieldId = $field->id;
            }

            // Store the layout sections as an entry element
            DB::table('entry_elements')->insert([
                'entry_id' => $entry->id,
                'field_id' => $fieldId,
                'handle' => 'page_layout',
                'value' => null,
                'meta' => json_encode($layout),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Clear the legacy layout column
        DB::table('entries')
            ->whereNotNull('layout')
            ->update(['layout' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not safely reversible as data mapping is lossy
    }
};
