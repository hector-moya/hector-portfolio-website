<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Enums\FieldType;
use Illuminate\Support\Str;

final readonly class FieldDto
{
    /**
     * @param  array<string, mixed>  $config
     * @param  array<string, mixed>  $validation
     */
    public function __construct(
        public string $name,
        public string $handle,
        public FieldType $type,
        public int $sortOrder = 0,
        public ?string $instructions = null,
        public bool $required = false,
        public array $config = [],
        public array $validation = [],
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            handle: $data['handle'] ?? Str::slug($data['name']),
            type: FieldType::from($data['type']),
            sortOrder: $data['sort_order'] ?? 0,
            instructions: $data['instructions'] ?? null,
            required: $data['required'] ?? false,
            config: $data['config'] ?? [],
            validation: $data['validation'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'handle' => $this->handle,
            'type' => $this->type->value,
            'sort_order' => $this->sortOrder,
            'instructions' => $this->instructions,
            'required' => $this->required,
            'config' => $this->config,
            'validation' => $this->validation,
        ];
    }
}
