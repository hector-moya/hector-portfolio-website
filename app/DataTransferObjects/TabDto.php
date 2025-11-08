<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

class TabDto
{
    /**
     * @param  array<string, FieldDto>  $fields
     * @param  array<int, SectionDto>  $sections
     */
    public function __construct(
        public readonly string $name,
        public readonly string $handle,
        public readonly int $sortOrder = 0,
        public array $sections = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            handle: $data['handle'] ?? \Illuminate\Support\Str::slug($data['name']),
            sortOrder: $data['sort_order'] ?? 0,
            sections: array_map(
                fn (array $section): \App\DataTransferObjects\SectionDto => SectionDto::fromArray($section),
                $data['sections'] ?? []
            ),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'handle' => $this->handle,
            'sort_order' => $this->sortOrder,
            'sections' => array_map(
                fn (SectionDto $section): array => $section->toArray(),
                $this->sections
            ),
        ];
    }
}
