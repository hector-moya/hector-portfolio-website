<?php

namespace App\FieldTypes;

abstract class BaseFieldType
{
    protected string $handle;

    protected bool $is_required = false;

    protected mixed $value = null;

    public function __construct(protected array $config = []) {}

    abstract public function name(): string;

    abstract public function label(): string;

    abstract public function view(): string;

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }

    public function setHandle(string $handle): self
    {
        $this->handle = $handle;

        return $this;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function setRequired(bool $required): self
    {
        $this->is_required = $required;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->is_required;
    }

    public function setValue(mixed $value): self
    {
        $this->value = $this->hydrate($value);

        return $this;
    }

    public function getValue(): mixed
    {
        return $this->dehydrate($this->value);
    }

    public function hydrate(mixed $value): mixed
    {
        return $value;
    }

    public function dehydrate(mixed $value): mixed
    {
        return $value;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->name(),
            'handle' => $this->handle,
            'is_required' => $this->is_required,
            'config' => $this->config,
        ];
    }
}
