<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class FieldForm extends Form
{
    public ?int $blueprint_id = null;
    public string $type = '';
    public string $label = '';
    public string $handle = '';
    public string $instructions = '';
    public array $config = [];
    public bool $is_required = false;
    public ?int $order = null;

}
