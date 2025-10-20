<?php

namespace App\Livewire\Blueprints;

use App\Livewire\Forms\BlueprintForm;
use App\Models\Blueprint;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component
{
    public BlueprintForm $form;

    public Blueprint $blueprint;

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

    public function mount(Blueprint $blueprint): void
    {
        $this->blueprint = $blueprint->load('elements');
        $this->form->setBlueprint($blueprint);
    }

    public function addElement(string $type): void
    {
        $this->form->addElement($type);
    }

    public function removeElement(int $index): void
    {
        $this->form->removeElement($index);
    }

    public function save(): void
    {
        $this->form->validate();

        $this->form->update($this->blueprint->id);

        session()->flash('message', 'Blueprint updated successfully.');

        $this->redirect(route('blueprints.index'), navigate: true);
    }

    #[Title('Edit Blueprint')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.edit');
    }
}
