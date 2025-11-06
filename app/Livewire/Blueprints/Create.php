<?php

namespace App\Livewire\Blueprints;

use App\Enums\FieldType;
use App\Livewire\Forms\BlueprintForm;
use App\Services\FieldTypeRegistry;
use App\Traits\HasSlug;
use Flux\Flux;
use Ramsey\Uuid\Uuid;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

class Create extends Component
{
    use HasSlug;

    public BlueprintForm $form;

    public function mount(): void
    {
        // Initialize with Main and Side tabs
        $this->form->tabs = [
            [
                'id' => UUID::uuid4()->toString(),
                'name' => __('Main'),
                'handle' => 'main',
                'sort_order' => 0,
                'sections' => [
                    [
                        'id' => UUID::uuid4()->toString(),
                        'name' => __('Content'),
                        'handle' => 'content',
                        'sort_order' => 0,
                        'instructions' => '',
                        'fields' => [
                            [
                                'id' => UUID::uuid4()->toString(),
                                'name' => __('Title'),
                                'handle' => 'title',
                                'type' => 'text',
                                'icon' => FieldType::Text->icon(),
                                'sort_order' => 0,
                                'instructions' => __('The title of your content'),
                                'is_required' => true,
                                'config' => app(FieldTypeRegistry::class)->defaultConfigFor('text'),
                                'validation' => [],
                            ],
                            [
                                'id' => UUID::uuid4()->toString(),
                                'name' => __('Content'),
                                'handle' => 'content',
                                'type' => 'richtext',
                                'icon' => FieldType::RichText->icon(),
                                'sort_order' => 1,
                                'instructions' => __('The main content body'),
                                'is_required' => true,
                                'config' => app(FieldTypeRegistry::class)->defaultConfigFor('rich_text'),
                                'validation' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'id' => UUID::uuid4()->toString(),
                'name' => __('Side'),
                'handle' => 'side',
                'sort_order' => 1,
                'sections' => [
                    [
                        'id' => UUID::uuid4()->toString(),
                        'name' => __('Meta'),
                        'handle' => 'meta',
                        'sort_order' => 0,
                        'instructions' => '',
                        'fields' => [
                            [
                                'id' => UUID::uuid4()->toString(),
                                'name' => __('Excerpt'),
                                'handle' => 'excerpt',
                                'type' => 'textarea',
                                'icon' => FieldType::Textarea->icon(),
                                'sort_order' => 0,
                                'instructions' => __('A brief summary of the content'),
                                'is_required' => false,
                                'config' => app(FieldTypeRegistry::class)->defaultConfigFor('textarea'),
                                'validation' => [],
                            ],
                        ],
                    ],
                ],
            ],
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

    #[On('tab-removed')]
    public function removeTab(array $data): void
    {
        dd($data);
    }

    #[On('tab-added')]
    public function addTab(array $data): void
    {
        $tab = $data['tab'] ?? [];
        $this->form->tabs[] = $tab;
    }

    #[On('field-added')]
    public function addField(array $data): void
    {
        $section = $data['section'] ?? [];
        $tabId = $data['tabId'] ?? '';
        $field = $data['fieldType'] ?? '';

        foreach ($this->form->tabs as $tabIndex => $tab) {
            if ($tab['id'] === $tabId) {
                foreach ($tab['sections'] as $sectionIndex => $existingSection) {
                    if ($existingSection['id'] === $section['id']) {
                        $this->form->tabs[$tabIndex]['sections'][$sectionIndex]['fields'][] = $field;
                        return;
                    }
                }
            }
        }

    }

    #[On('update-section')]
    public function updateSection(array $data): void
    {
        $section = $data['section'] ?? [];
        $tabId = $data['tabId'] ?? '';

        foreach ($this->form->tabs as $tabIndex => $tab) {
            if ($tab['id'] === $tabId) {
                foreach ($tab['sections'] as $sectionIndex => $existingSection) {
                    if ($existingSection['id'] === $section['id']) {
                        $this->form->tabs[$tabIndex]['sections'][$sectionIndex] = $section;
                        return;
                    }
                }
            }
        }
    }

    public function save(): void
    {
        $this->form->create();

        $this->redirect(route('blueprints.index'), navigate: true);
    }

    #[Title('Create Blueprint')]
    public function render(): View|Factory
    {
        return view('livewire.blueprints.create');
    }
}
