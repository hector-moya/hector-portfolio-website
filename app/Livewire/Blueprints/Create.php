<?php

namespace App\Livewire\Blueprints;

use App\Livewire\Actions\Blueprints\CreateBlueprint;
use App\Livewire\Forms\BlueprintForm;
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
        $this->form->addElement();
    }

    public function addElement(): void
    {
        $this->form->addElement();
    }

    public function removeElement(int $index): void
    {
        $this->form->removeElement($index);
    }

    public function save(): void
    {
        $this->form->validate();

        (new CreateBlueprint)->execute(
            $this->form->only(['name', 'slug', 'description', 'is_active']),
            $this->form->elements
        );

        session()->flash('message', 'Blueprint created successfully.');

        $this->redirect(route('blueprints.index'), navigate: true);
    }

    #[Title('Create Blueprint')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.create');
    }
}
