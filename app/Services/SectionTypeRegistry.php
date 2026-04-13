<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\SectionType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class SectionTypeRegistry
{
    /**
     * Returns metadata for all section types.
     *
     * @return array<int, array{value: string, label: string, icon: string, default_config: array<string, mixed>}>
     */
    public function all(): array
    {
        return array_map(fn (SectionType $type): array => [
            'value' => $type->value,
            'label' => $type->label(),
            'icon' => $type->icon(),
            'default_config' => $type->defaultConfig(),
        ], SectionType::cases());
    }

    /**
     * Returns [value => label] pairs for selects.
     *
     * @return array<string, string>
     */
    public function optionsForSelect(): array
    {
        $options = [];
        foreach (SectionType::cases() as $type) {
            $options[$type->value] = $type->label();
        }

        return $options;
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultDataFor(string $value): array
    {
        $type = SectionType::tryFrom($value);

        return $type?->defaultData() ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultConfigFor(string $value): array
    {
        $type = SectionType::tryFrom($value);

        return $type?->defaultConfig() ?? [];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function configRulesFor(string $value): array
    {
        $type = SectionType::tryFrom($value);

        return $type?->configRules() ?? [];
    }

    /**
     * Validate a field element's config array against its section type.
     */
    public function validateConfig(array $element, int $index): void
    {
        $type = SectionType::tryFrom($element['type'] ?? '');
        if (! $type) {
            return;
        }

        $rules = $type->configRules();
        if ($rules === []) {
            return;
        }

        $data = $element['config'] ?? [];
        $validator = Validator::make($data, $rules);
        throw_if($validator->fails(), ValidationException::withMessages(
            collect($validator->errors()->toArray())->mapWithKeys(fn ($messages, $key): array => [sprintf('elements.%d.config.%s', $index, $key) => $messages])->all()
        ));
    }
}
