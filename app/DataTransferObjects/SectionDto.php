<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

class SectionDto
{
    /**
     * @param  array<string, FieldDto>  $fields
     */
    public function __construct(
        public readonly string $name,
        public readonly string $handle,
        public readonly int $sortOrder = 0,
        public readonly ?string $instructions = null,
        public array $fields = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            handle: $data['handle'] ?? \Illuminate\Support\Str::slug($data['name']),
            sortOrder: $data['sort_order'] ?? 0,
            instructions: $data['instructions'] ?? null,
            fields: array_map(
                fn (array $field): \App\DataTransferObjects\FieldDto => FieldDto::fromArray($field),
                $data['fields'] ?? []
            ),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'handle' => $this->handle,
            'sort_order' => $this->sortOrder,
            'instructions' => $this->instructions,
            'fields' => array_map(
                fn (FieldDto $field): array => $field->toArray(),
                $this->fields
            ),
        ];
    }
}
