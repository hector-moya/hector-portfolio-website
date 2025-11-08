<?php

namespace App\Traits\Blueprints;

use App\Enums\FieldType;
use App\Services\FieldTypeRegistry;
use Ramsey\Uuid\Uuid;

trait Sections
{
    public function newSection(array $data): array
    {
        return [
            'id' => UUID::uuid4()->toString(),
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
        $defaultConfig = app(FieldTypeRegistry::class)->defaultConfigFor($type);

        return [
            'id' => Uuid::uuid4()->toString(),
            'name' => FieldType::from($type)->defaultLabel(),
            'type' => $type,
            'icon' => FieldType::from($type)->icon(),
            'label' => FieldType::from($type)->defaultLabel(),
            'handle' => '',
            'instructions' => '',
            'is_required' => false,
            'config' => $defaultConfig,
        ];
    }
}
