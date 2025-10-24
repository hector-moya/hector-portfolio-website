<?php

namespace App\Services;

use App\Enums\FieldType;

class FieldTypeRegistry
{
    /**
     * Returns an array of metadata for all field types.
     * Each item: ['value' => string, 'label' => string, 'icon' => string, 'default_config' => array]
     */
    public function all(): array
    {
        return array_map(fn (FieldType $type): array => [
            'value' => $type->value,
            'label' => $type->label(),
            'icon' => $type->icon(),
            'default_config' => $type->defaultConfig(),
        ], FieldType::cases());
    }

    /**
     * Returns [value => label] pairs for selects.
     */
    public function optionsForSelect(): array
    {
        $options = [];
        foreach (FieldType::cases() as $type) {
            $options[$type->value] = $type->label();
        }

        return $options;
    }

    public function defaultConfigFor(string $value): array
    {
        foreach (FieldType::cases() as $type) {
            if ($type->value === $value) {
                return $type->defaultConfig();
            }
        }

        return [];
    }
}
