<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use App\Services\FieldTypeRegistry;
use App\Enums\FieldType;
use Livewire\Form;

class SectionForm extends Form
{
    public $fields = [];
    
    public function addField(string $type): void
    {
        $defaultConfig = app(FieldTypeRegistry::class)->defaultConfigFor($type);

        $this->fields[] = [
            'type' => $type,
            'icon' => FieldType::from($type)->icon(),
            'label' => FieldType::from($type)->defaultLabel(),
            'handle' => '',
            'instructions' => '',
            'is_required' => false,
            'config' => $defaultConfig,
        ];
    }
}
