<?php

declare(strict_types=1);

namespace App\Livewire\Entries;

use App\Livewire\Forms\EntryForm;
use App\Models\Blueprint;
use App\Models\Entry;
use App\Support\SectionTypes;
use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

final class Edit extends Component
{
    use WithFileUploads;

    public EntryForm $form;

    public array $uploads = [];

    public Entry $entry;

    /** @var array<string, string> Pending section type per page builder field handle */
    public array $pendingSectionTypes = [];

    public function mount(Entry $entry): void
    {
        $this->entry = $entry->load('collection.blueprint.tabs.sections.fields', 'collection.blueprint.fields', 'elements.field');
        $this->form->setEntry($this->entry);
    }

    public function updatedFormTitle(): void
    {
        if (in_array($this->form->slug, ['', '0', Str::slug($this->entry->title)], true)) {
            $this->form->slug = Str::slug($this->form->title);
        }
    }

    #[Computed]
    public function blueprint(): ?Blueprint
    {
        if ($this->form->blueprint_id === null || $this->form->blueprint_id === 0) {
            return null;
        }

        return Blueprint::with(['tabs.sections.fields', 'fields'])->find($this->form->blueprint_id);
    }

    #[On('asset-uploaded')]
    public function save(): void
    {
        $this->form->validate();
        $this->form->update($this->form->entry->id);
    }

    public function addRepeaterItem(string $handle): void
    {
        $this->form->addRepeaterItem($handle);
    }

    public function removeRepeaterItem(string $handle, int $index): void
    {
        $this->form->removeRepeaterItem($handle, $index);
    }

    public function openAssetBrowser(string $handle): void
    {
        Flux::modal('asset-browser-'.$handle)->show();
    }

    /* ---------- Page Builder operations ---------- */

    public function addPageBuilderSection(string $handle): void
    {
        $sectionType = $this->pendingSectionTypes[$handle] ?? '';

        if (SectionTypes::get($sectionType) === null) {
            return;
        }

        $this->form->fieldValues[$handle][] = [
            '_id' => (string) Str::uuid(),
            'type' => $sectionType,
            'data' => SectionTypes::defaults($sectionType),
        ];

        $this->pendingSectionTypes[$handle] = '';
        Flux::modal('add-section-'.$handle)->close();
    }

    public function removePageBuilderSection(string $handle, int $index): void
    {
        array_splice($this->form->fieldValues[$handle], $index, 1);
    }

    public function movePageBuilderSectionUp(string $handle, int $index): void
    {
        if ($index === 0) {
            return;
        }

        $sections = $this->form->fieldValues[$handle];
        [$sections[$index - 1], $sections[$index]] = [$sections[$index], $sections[$index - 1]];
        $this->form->fieldValues[$handle] = $sections;
    }

    public function movePageBuilderSectionDown(string $handle, int $index): void
    {
        $sections = $this->form->fieldValues[$handle];

        if ($index >= count($sections) - 1) {
            return;
        }

        [$sections[$index], $sections[$index + 1]] = [$sections[$index + 1], $sections[$index]];
        $this->form->fieldValues[$handle] = $sections;
    }

    public function addPageBuilderFeatureItem(string $handle, int $sectionIndex): void
    {
        $this->form->fieldValues[$handle][$sectionIndex]['data']['items'][] = [
            'icon' => '',
            'item_title' => '',
            'item_description' => '',
        ];
    }

    public function removePageBuilderFeatureItem(string $handle, int $sectionIndex, int $itemIndex): void
    {
        array_splice($this->form->fieldValues[$handle][$sectionIndex]['data']['items'], $itemIndex, 1);
    }

    public function removePageBuilderSectionImage(string $handle, int $sectionIndex, string $field): void
    {
        $this->form->fieldValues[$handle][$sectionIndex]['data'][$field] = null;
    }

    public function removePageBuilderGalleryImage(string $handle, int $sectionIndex, int $imageIndex): void
    {
        array_splice($this->form->fieldValues[$handle][$sectionIndex]['data']['images'], $imageIndex, 1);
    }

    /* ---------- Asset selection ---------- */

    #[On('asset-selected')]
    public function onAssetSelected(array $data): void
    {
        $assetHandle = $data['handle'];

        // Page builder asset handles: section_{uuid}_{fieldName}
        if (str_starts_with((string) $assetHandle, 'section_')) {
            $this->handlePageBuilderAsset($assetHandle, $data['value']);

            return;
        }

        // Regular form field
        $this->form->fieldValues[$assetHandle] = $data['value'];
    }

    public function render(): View|Factory
    {
        return view('livewire.entries.edit');
    }

    private function handlePageBuilderAsset(string $assetHandle, mixed $value): void
    {
        if (! preg_match('/^section_([0-9a-f-]{36})_(.+)$/', $assetHandle, $matches)) {
            return;
        }

        $sectionId = $matches[1];
        $fieldPart = $matches[2];

        // Find which page_builder field contains this section
        foreach ($this->form->fieldValues as $handle => $fieldValue) {
            if (! is_array($fieldValue)) {
                continue;
            }

            foreach ($fieldValue as $i => $section) {
                if (! isset($section['_id'])) {
                    continue;
                }

                if ($section['_id'] !== $sectionId) {
                    continue;
                }

                // Gallery slot: image_{slotIndex}
                if (preg_match('/^image_(\d+)$/', $fieldPart)) {
                    $images = $this->form->fieldValues[$handle][$i]['data']['images'] ?? [];
                    if (count($images) < 6) {
                        $this->form->fieldValues[$handle][$i]['data']['images'][] = $value;
                    }
                } else {
                    $this->form->fieldValues[$handle][$i]['data'][$fieldPart] = $value;
                }

                return;
            }
        }
    }
}
