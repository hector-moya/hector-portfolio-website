<?php

declare(strict_types=1);

namespace App\Traits\Blueprints;

use App\Enums\SectionType;
use Ramsey\Uuid\Uuid;

trait Sections
{
    public function newSection(array $data): array
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'name' => __('New Section'),
            'blueprint_id' => $data['blueprint_id'] ?? null,
            'tab_id' => $data['tab_id'] ?? null,
            'handle' => '',
            'sort_order' => 0,
            'instructions' => '',
            'fields' => [],
        ];
    }

    public function newField(string $type): array
    {
        $sectionType = SectionType::tryFrom($type);

        return [
            'id' => Uuid::uuid4()->toString(),
            'name' => $sectionType?->defaultLabel() ?? 'New Field',
            'type' => $type,
            'icon' => $sectionType?->icon() ?? 'document-text',
            'label' => $sectionType?->defaultLabel() ?? 'New Field',
            'handle' => '',
            'instructions' => '',
            'is_required' => false,
            'config' => $sectionType?->defaultConfig() ?? [],
        ];
    }
}
