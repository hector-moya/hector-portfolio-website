<?php

namespace App\Livewire\Blueprints;

use App\Livewire\Forms\BlueprintForm;
use App\Models\Blueprint;
use App\Services\FieldTypeRegistry;
use Flux\Flux;
use Livewire\Attributes\Title;
use Livewire\Component;

class Edit extends Component
{
    public BlueprintForm $form;

    public Blueprint $blueprint;

    /**
     * Select options [value => label] built from registry.
     */
    #[\Livewire\Attributes\Computed]
    public function fieldTypeOptions(): array
    {
        return app(FieldTypeRegistry::class)->optionsForSelect();
    }

    /**
     * Meta for modal listing (value, label, icon).
     */
    #[\Livewire\Attributes\Computed]
    public function fieldTypeMeta(): array
    {
        return app(FieldTypeRegistry::class)->all();
    }

    public function mount(Blueprint $blueprint): void
    {
        $this->blueprint = $blueprint->load('elements');
        $this->form->setBlueprint($blueprint);
    }

    public function addElement(string $type): void
    {
        $this->form->addElement(type: $type);
        Flux::modal('select-field-modal')->close();
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

    public function removeElement(int $index): void
    {
        $this->form->removeElement($index);
    }

    public function save(): void
    {
        $this->form->update($this->blueprint->id);
    }

    #[Title('Edit Blueprint')]
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.edit');
    }
}
