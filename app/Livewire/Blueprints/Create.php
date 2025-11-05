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
                                'required' => true,
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
                                'required' => true,
                                'config' => app(FieldTypeRegistry::class)->defaultConfigFor('rich_text'),
                                'validation' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
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
                                'required' => false,
                                'config' => app(FieldTypeRegistry::class)->defaultConfigFor('textarea'),
                                'validation' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];
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
            [
                'id' => 'main-content',
                'name' => 'Main Content',
                'instructions' => 'Primary content for this blueprint',
                'fields' => [],
            ],
            [
                'id' => 'side-content',
                'name' => 'Side Content',
                'instructions' => 'Supplementary content for this blueprint',
                'fields' => [],
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
