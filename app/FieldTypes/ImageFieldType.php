<?php

declare(strict_types=1);

namespace App\FieldTypes;

use App\Models\Asset;

final class ImageFieldType extends BaseFieldType
{
    public function name(): string
    {
        return 'image';
    }

    public function label(): string
    {
        return 'Image';
    }

    public function view(): string
    {
        return 'field-types.image';
    }

    protected function fieldRules(): array
    {
        return [
            'required_if:is_required,true',
            'nullable',
            'exists:assets,id',
        ];
    }

    protected function fieldMessages(): array
    {
        return [
            'exists' => 'Please select a valid image from the media library.',
        ];
    }

    protected function transformHydrate(mixed $value): mixed
    {
        if (! $value) {
            return null;
        }

        return Asset::query()->find($value);
    }

    protected function transformDehydrate(mixed $value): mixed
    {
        if (! $value) {
            return null;
        }

        return $value instanceof Asset ? $value->id : $value;
    }
}
