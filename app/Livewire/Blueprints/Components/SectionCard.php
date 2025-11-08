<?php

namespace App\Livewire\Blueprints\Components;

use App\Livewire\Forms\SectionForm;
use App\Services\FieldTypeRegistry;
use App\Traits\HasSlug;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class SectionCard extends Component
{
    use HasSlug;

    public SectionForm $form;

    public ?int $sectionId = null;

    #[On('field-added')]
    public function mount(): void
    {
        $this->form->setSection($this->sectionId);
    }

    public function updateFormName(): void
    {
        $this->form->handle = $this->generateSlug($this->form->name);
    }

    #[Computed]
    public function fieldTypeMeta(): array
    {
        return app(FieldTypeRegistry::class)->all();
    }

    public function addField(string $type): void
    {
        $this->form->addField($this->sectionId, $type);

        $this->dispatch('field-added');

        Flux::modal('select-field-modal-'.$this->sectionId)->close();
    }

    public function updateSection(int $id): void
    {
        $this->form->update($this->sectionId);

        $this->dispatch('update-section');

        Flux::modal('edit-section-modal-'.$id)->close();
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.components.section-card');
    }
}
