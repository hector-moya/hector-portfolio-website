<?php

namespace App\Livewire\Blueprints;

use App\Enums\FieldType;
use App\Livewire\Forms\BlueprintForm;
use App\Services\FieldTypeRegistry;
use App\Traits\HasSlug;
use Flux\Flux;
use Ramsey\Uuid\Uuid;
use App\Traits\Blueprints\Tabs;
use App\Traits\Blueprints\Sections;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    use HasSlug, Tabs, Sections;

    public BlueprintForm $form;
    public string $newTabName = '';

    public array $editingTab = [
        'name' => '',
    ];

    public function mount(): void
    {
        $this->form->tabs = $this->initializeTabs();
    }

    #[Computed]
    public function fieldTypeMeta(): array
    {
        return app(FieldTypeRegistry::class)->all();
    }


    public function updatedFormName(): void
    {
        $this->form->slug = $this->form->generateSlug($this->form->name);
    }

    // public function updated($propertyName, $value): void
    // {
    //     if (preg_match('/^form\.elements\.(\d+)\.label$/', (string) $propertyName, $matches)) {
    //         $this->form->updateHandleFromLabel((int) $matches[1]);
    //     }
    // }

    public function removeTab(int $index): void
    {
        unset($this->form->tabs[$index]);
    }

    public function addTab(): void
    {
        if (! trim($this->newTabName)) {
            return;
        }

        $this->form->tabs[] = $this->createTab($this->newTabName);

        $this->newTabName = '';

        Flux::modal('add-tab-modal')->close();
    }

    #[On('field-added')]
    public function addField(array $data): void
    {
        $tabIndex = $data['tabIndex'];
        $sectionIndex = $data['sectionIndex'];

        $this->form->tabs[$tabIndex]['sections'][$sectionIndex]['fields'][] = $this->newField($data['type']);

    }

    public function updateSection(string $id): void
    {
        Flux::modal('edit-section-modal-' . $id)->close();
    }

    public function addSection(int $index): void
    {
        $this->form->tabs[$index]['sections'][] = $this->newSection();
    }

    public function save(): void
    {
        dd('Hello from saving blueprint!');
        $this->form->create();

        $this->redirect(route('blueprints.index'), navigate: true);
    }

    #[Title('Create Blueprint')]
    public function render(): View|Factory
    {
        return view('livewire.blueprints.create', [
            'tabs' => $this->form->tabs,
        ]);
    }
}
