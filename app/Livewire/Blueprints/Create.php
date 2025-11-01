<?php

namespace App\Livewire\Blueprints;

use App\Livewire\Forms\BlueprintForm;
use App\Services\FieldTypeRegistry;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    public BlueprintForm $form;

    /**
     * Select options [value => label] built from registry.
     */
    #[Computed]
    public function fieldTypeOptions(): array
    {
        return app(FieldTypeRegistry::class)->optionsForSelect();
    }

    /**
     * Meta for modal listing (value, label, icon).
     */
    #[Computed]
    public function fieldTypeMeta(): array
    {
        return app(FieldTypeRegistry::class)->all();
    }

    public function mount(): void
    {
        // Start with one empty element
        $this->form->addElement('text');
    }

    public function updatedFormName(): void
    {
        $this->form->slug = $this->form->generateSlug($this->form->name);
    }

    public function updated($propertyName, $value): void
    {
        if (preg_match('/^form\.elements\.(\d+)\.label$/', (string) $propertyName, $matches)) {
            $this->form->updateHandleFromLabel((int) $matches[1]);
        }
    }

    public function addElement(string $type): void
    {
        $this->form->addElement(type: $type);
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

    public function addNestedField(int $parentIndex, string $type = 'text'): void
    {
        $this->form->addNestedField($parentIndex, $type);
    }

    public function removeNestedField(int $parentIndex, int $childIndex): void
    {
        $this->form->removeNestedField($parentIndex, $childIndex);
    }

    public function addOption(int $index): void
    {
        $this->form->addOption($index);
    }

    public function removeOption(int $index, int $optIndex): void
    {
        $this->form->removeOption($index, $optIndex);
    }

    #[Title('Create Blueprint')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.create');
    }
}
