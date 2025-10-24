<?php

namespace App\Enums;

enum FieldType: string
{
    case Text = 'text';
    case Textarea = 'textarea';
    case RichText = 'richtext';
    case Number = 'number';
    case Email = 'email';
    case Url = 'url';
    case Date = 'date';
    case Time = 'time';
    case DateTime = 'datetime';
    case Checkbox = 'checkbox';
    case Select = 'select';
    case Radio = 'radio';
    case Image = 'image';
    case File = 'file';
    case Repeater = 'repeater';

    public function label(): string
    {
        return match ($this) {
            self::Text => 'Text (Single Line)',
            self::Textarea => 'Text (Multi-line)',
            self::RichText => 'Content (Rich Text)',
            self::Number => 'Number',
            self::Email => 'Email',
            self::Url => 'URL',
            self::Date => 'Date',
            self::Time => 'Time',
            self::DateTime => 'Date & Time',
            self::Checkbox => 'Toggle',
            self::Select => 'Select',
            self::Radio => 'Radio',
            self::Image => 'Image',
            self::File => 'File',
            self::Repeater => 'Repeater (Block Group)',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Text => 'language',
            self::Textarea => 'document-text',
            self::RichText => 'book-open',
            self::Number => 'calculator',
            self::Email => 'at-symbol',
            self::Url => 'link',
            self::Date => 'calendar',
            self::Time => 'clock',
            self::DateTime => 'calendar-date-range',
            self::Checkbox => 'document-check',
            self::Select => 'chevrons-up-down',
            self::Radio => 'list-bullet',
            self::Image => 'photo',
            self::File => 'arrow-up-tray',
            self::Repeater => 'rectangle-group',
        };
    }

    public function defaultConfig(): array
    {
        return match ($this) {
            self::Text => ['placeholder' => null, 'max' => null],
            self::Textarea => ['rows' => 4, 'max' => null],
            self::RichText => ['toolbar' => ['bold', 'italic', 'link', 'h2', 'h3', 'ul', 'ol']],
            self::Number => ['min' => null, 'max' => null, 'step' => 1],
            self::Email => ['placeholder' => null],
            self::Url => ['placeholder' => null],
            self::Date => ['format' => 'Y-m-d'],
            self::Time => ['format' => 'H:i'],
            self::DateTime => ['format' => 'Y-m-d H:i'],
            self::Checkbox => ['on_label' => 'Yes', 'off_label' => 'No'],
            self::Select => ['options' => []],
            self::Radio => ['options' => []],
            self::Image => ['max_size_mb' => 5, 'mimes' => ['jpg', 'jpeg', 'png', 'webp']],
            self::File => ['max_size_mb' => 10, 'mimes' => ['pdf', 'doc', 'docx']],
            self::Repeater => ['blueprint' => [], 'min' => 0, 'max' => null],
        };
    }

    /**
     * Validation rules for the *config* of each type.
     * These are applied while saving the Blueprint.
     */
    public function configRules(): array
    {
        return match ($this) {
            self::Text => [
                'placeholder' => ['nullable', 'string', 'max:255'],
                'max' => ['nullable', 'integer', 'min:1'],
            ],
            self::Textarea => [
                'rows' => ['nullable', 'integer', 'min:1', 'max:50'],
                'max' => ['nullable', 'integer', 'min:1'],
            ],
            self::RichText => [
                'toolbar' => ['array'],
                'toolbar.*' => ['string'],
                'max' => ['nullable', 'integer', 'min:1'],
            ],
            self::Number => [
                'min' => ['nullable', 'numeric'],
                'max' => ['nullable', 'numeric'],
                'step' => ['nullable', 'numeric', 'min:0.0000001'],
            ],
            self::Email, self::Url => [
                'placeholder' => ['nullable', 'string', 'max:255'],
            ],
            self::Date, self::Time, self::DateTime => [
                'format' => ['required', 'string', 'max:50'],
            ],
            self::Checkbox => [
                'on_label' => ['required', 'string', 'max:50'],
                'off_label' => ['required', 'string', 'max:50'],
            ],
            self::Select, self::Radio => [
                'options' => ['array', 'min:1'],
                'options.*.value' => ['required', 'string', 'max:255'],
                'options.*.label' => ['required', 'string', 'max:255'],
            ],
            self::Image, self::File => [
                'max_size_mb' => ['required', 'integer', 'min:1', 'max:512'],
                'mimes' => ['array', 'min:1'],
                'mimes.*' => ['string'],
            ],
            self::Repeater => [
                'min' => ['nullable', 'integer', 'min:0'],
                'max' => ['nullable', 'integer', 'min:1'],
                'blueprint' => ['array'],
                'blueprint.*.type' => ['required', 'string'],
                'blueprint.*.label' => ['required', 'string', 'max:255'],
                'blueprint.*.handle' => ['required', 'string', 'max:255'],
                'blueprint.*.instructions' => ['nullable', 'string'],
                'blueprint.*.is_required' => ['boolean'],
                'blueprint.*.config' => ['array'],
            ],
        };
    }
}
