<?php

namespace App\Services;

use App\Enums\FieldType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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

    public function configRulesFor(string $value): array
    {
        foreach (FieldType::cases() as $type) {
            if ($type->value === $value) {
                return $type->configRules();
            }
        }

        return [];
    }

    /**
     * Validate a single element's config array against its type.
     */
    public function validateConfig(array $element, int $index): void
    {
        $type = collect(FieldType::cases())->firstWhere('value', $element['type'] ?? null);
        if (! $type) {
            return;
        }

        $rules = $type->configRules();

        // namespacing for nicer error keys: elements.{i}.config.*
        $data = $element['config'] ?? [];
        $validator = Validator::make($data, $rules);
        throw_if($validator->fails(), ValidationException::withMessages(
            collect($validator->errors()->toArray())->mapWithKeys(fn ($messages, $key): array => ["elements.$index.config.$key" => $messages])->all()
        ));
    }
}
