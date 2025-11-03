<?php

namespace App\Livewire\Blueprints;

use App\Livewire\Forms\BlueprintForm;
use App\Services\FieldTypeRegistry;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Flux\Flux;
use App\Traits\HasSlug;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    use HasSlug;
    public BlueprintForm $form;

    public function mount(): void
    {
        // Start with one empty element
        $this->form->addField('text');
    }
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

    #[Computed]
    public function sections(): array
    {
        return $this->form->sections = [
            ['id' => 1, 'name' => 'Section 1'],
        ];
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

    public function addField(string $type): void
    {
        $this->form->addField(type: $type);
        Flux::modal('select-field-modal')->close();
    }

    public function removeField(int $index): void
    {
        $this->form->removeField($index);
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
    public function render(): View|Factory
    {
        return view('livewire.blueprints.create');
    }
}
