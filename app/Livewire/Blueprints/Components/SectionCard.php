<?php

declare(strict_types=1);

namespace App\Livewire\Blueprints\Components;

use App\Livewire\Forms\SectionForm;
use App\Services\SectionTypeRegistry;
use App\Traits\HasSlug;
use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

final class SectionCard extends Component
{
    use HasSlug;

    public SectionForm $form;

    public ?int $sectionId = null;

    #[On(['field-added', 'field-removed'])]
    public function mount(): void
    {
        $this->form->setSection($this->sectionId);
    }

    public function updatedFormName(): void
    {
        $this->form->handle = $this->generateSlug($this->form->name);
    }

    #[Computed]
    public function fieldTypeMeta(): array
    {
        return resolve(SectionTypeRegistry::class)->all();
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

    public function removeSection(): void
    {
        $this->form->destroy($this->sectionId);

        $this->dispatch('section-removed');
    }

    public function render(): View|Factory
    {
        return view('livewire.blueprints.components.section-card');
    }
}
