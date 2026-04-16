<?php

declare(strict_types=1);

namespace App\FieldTypes;

use App\Models\Asset;

final class FileFieldType extends BaseFieldType
{
    public function name(): string
    {
        return 'file';
    }

    public function label(): string
    {
        return 'File';
    }

    public function view(): string
    {
        return 'field-types.file';
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
            'exists' => 'Please select a valid file from the media library.',
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
