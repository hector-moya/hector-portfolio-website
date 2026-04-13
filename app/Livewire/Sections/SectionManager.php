<?php

declare(strict_types=1);

namespace App\Livewire\Sections;

use App\Enums\SectionType;
use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;

final class SectionManager extends Component
{
    /** @var list<array{_id: string, type: string, data: array<string, mixed>}> */
    #[Modelable]
    public array $sections = [];

    public string $pendingSectionType = '';

    public function addSection(): void
    {
        $type = SectionType::tryFrom($this->pendingSectionType);
        if (! $type) {
            return;
        }

        $this->sections[] = [
            '_id' => (string) Str::uuid(),
            'type' => $type->value,
            'data' => $type->defaultData(),
        ];

        $this->pendingSectionType = '';
        Flux::modal('add-section')->close();
    }

    public function removeSection(int $index): void
    {
        array_splice($this->sections, $index, 1);
    }

    public function moveSectionUp(int $index): void
    {
        if ($index === 0) {
            return;
        }

        [$this->sections[$index - 1], $this->sections[$index]] = [$this->sections[$index], $this->sections[$index - 1]];
    }

    public function moveSectionDown(int $index): void
    {
        if ($index >= count($this->sections) - 1) {
            return;
        }

        [$this->sections[$index], $this->sections[$index + 1]] = [$this->sections[$index + 1], $this->sections[$index]];
    }

    #[On('section-data-updated')]
    public function onSectionDataUpdated(string $sectionId, array $data): void
    {
        $index = array_find_key($this->sections, fn (array $section): bool => $section['_id'] === $sectionId);

        if ($index !== null) {
            $this->sections[$index]['data'] = $data;
        }
    }

    #[On('asset-selected')]
    public function onAssetSelected(string $handle, mixed $value): void
    {
        if (! str_starts_with($handle, 'section_')) {
            return;
        }

        if (! preg_match('/^section_([0-9a-f-]{36})_(.+)$/', $handle, $matches)) {
            return;
        }

        $sectionId = $matches[1];
        $fieldPart = $matches[2];
        $sectionIndex = array_find_key($this->sections, fn (array $section): bool => $section['_id'] === $sectionId);

        if ($sectionIndex === null) {
            return;
        }

        // Gallery slot: image_{slotIndex}
        if (preg_match('/^image_(\d+)$/', $fieldPart)) {
            $images = $this->sections[$sectionIndex]['data']['images'] ?? [];
            if (count($images) < 6) {
                $this->sections[$sectionIndex]['data']['images'][] = $value;
            }

            return;
        }

        $this->sections[$sectionIndex]['data'][$fieldPart] = $value;
    }

    public function addFeatureItem(int $sectionIndex): void
    {
        if (! isset($this->sections[$sectionIndex]['data']['items'])) {
            $this->sections[$sectionIndex]['data']['items'] = [];
        }

        $this->sections[$sectionIndex]['data']['items'][] = [
            'icon' => '',
            'item_title' => '',
            'item_description' => '',
        ];
    }

    public function removeFeatureItem(int $sectionIndex, int $itemIndex): void
    {
        array_splice($this->sections[$sectionIndex]['data']['items'], $itemIndex, 1);
    }

    public function removeSectionImage(int $sectionIndex, string $field): void
    {
        $this->sections[$sectionIndex]['data'][$field] = null;
    }

    public function removeGalleryImage(int $sectionIndex, int $imageIndex): void
    {
        array_splice($this->sections[$sectionIndex]['data']['images'], $imageIndex, 1);
    }

    public function addFormField(int $sectionIndex): void
    {
        if (! isset($this->sections[$sectionIndex]['data']['fields'])) {
            $this->sections[$sectionIndex]['data']['fields'] = [];
        }

        $this->sections[$sectionIndex]['data']['fields'][] = [
            'type' => 'text',
            'label' => '',
            'handle' => '',
            'required' => false,
        ];
    }

    public function removeFormField(int $sectionIndex, int $fieldIndex): void
    {
        array_splice($this->sections[$sectionIndex]['data']['fields'], $fieldIndex, 1);
    }

    public function render(): View|Factory
    {
        return view('livewire.sections.section-manager', [
            'sectionTypes' => SectionType::cases(),
            'sectionTypeLabels' => collect(SectionType::cases())
                ->mapWithKeys(fn (SectionType $type): array => [$type->value => $type->label()])
                ->all(),
        ]);
    }
}
