<?php

declare(strict_types=1);

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

    final public function rules(): array
    {
        return $this->fieldRules();
    }

    final public function messages(): array
    {
        return $this->fieldMessages();
    }

    final public function setHandle(string $handle): self
    {
        $this->handle = $handle;

        return $this;
    }

    final public function getHandle(): string
    {
        return $this->handle;
    }

    final public function setRequired(bool $required): self
    {
        $this->is_required = $required;

        return $this;
    }

    final public function isRequired(): bool
    {
        return $this->is_required;
    }

    final public function setValue(mixed $value): self
    {
        $this->value = $this->hydrate($value);

        return $this;
    }

    final public function getValue(): mixed
    {
        return $this->dehydrate($this->value);
    }

    final public function hydrate(mixed $value): mixed
    {
        return $this->transformHydrate($value);
    }

    final public function dehydrate(mixed $value): mixed
    {
        return $this->transformDehydrate($value);
    }

    final public function toArray(): array
    {
        return [
            'type' => $this->name(),
            'handle' => $this->handle,
            'is_required' => $this->is_required,
            'config' => $this->config,
        ];
    }

    protected function fieldRules(): array
    {
        return [];
    }

    protected function fieldMessages(): array
    {
        return [];
    }

    protected function transformHydrate(mixed $value): mixed
    {
        return $value;
    }

    protected function transformDehydrate(mixed $value): mixed
    {
        return $value;
    }
}
