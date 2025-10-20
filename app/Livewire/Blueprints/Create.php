<?php

namespace App\Livewire\Blueprints;

use App\Livewire\Forms\BlueprintForm;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public BlueprintForm $form;

    public array $fieldTypes = [
        'text' => 'Text',
        'textarea' => 'Textarea',
        'richtext' => 'Rich Text',
        'number' => 'Number',
        'email' => 'Email',
        'url' => 'URL',
        'date' => 'Date',
        'time' => 'Time',
        'datetime' => 'Date & Time',
        'checkbox' => 'Checkbox',
        'select' => 'Select',
        'radio' => 'Radio',
        'image' => 'Image',
        'file' => 'File',
    ];

    public function mount(): void
    {
        // Start with one empty element
        $this->form->addElement('text');
    }

    public function addElement(string $type): void
    {
        $this->form->addElement($type);
        Flux::modal('select-field-modal')->close();
    }

    public function removeElement(int $index): void
    {
        $this->form->removeElement($index);
    }

    public function save(): void
    {
        $this->form->create();

        $this->redirect(route('blueprints.index'), navigate: true);
    }

    #[Title('Create Blueprint')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.create');
    }
}
