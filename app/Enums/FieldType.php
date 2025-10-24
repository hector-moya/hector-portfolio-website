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
            self::Repeater => ['blueprint' => []],
        };
    }
}
