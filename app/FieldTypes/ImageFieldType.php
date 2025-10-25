<?php

namespace App\FieldTypes;

use App\Models\Asset;

class ImageFieldType extends BaseFieldType
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

    public function rules(): array
    {
        return [
            'required_if:is_required,true',
            'nullable',
            'exists:assets,id',
        ];
    }

    public function messages(): array
    {
        return [
            'exists' => 'Please select a valid image from the media library.',
        ];
    }

    public function hydrate(mixed $value): mixed
    {
        if (! $value) {
            return null;
        }

        return \App\Models\Asset::query()->find($value);
    }

    public function dehydrate(mixed $value): mixed
    {
        if (! $value) {
            return null;
        }

        return $value instanceof Asset ? $value->id : $value;
    }
}
