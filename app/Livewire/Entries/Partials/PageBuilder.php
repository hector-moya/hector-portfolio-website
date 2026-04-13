<?php

declare(strict_types=1);

namespace App\Livewire\Entries\Partials;

use App\Livewire\Actions\SavePageLayout;
use App\Models\Entry;
use App\Support\SectionTypes;
use Flux\Flux;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

final class PageBuilder extends Component
{
    public Entry $entry;

    /** @var array<int, array{_id: string, type: string, data: array<string, mixed>}> */
    public array $sections = [];

    public string $pendingSectionType = '';

    public function mount(Entry $entry): void
    {
        $this->entry = $entry;
        $this->sections = $entry->layout ?? [];
    }

    public function addSection(): void
    {
        if (SectionTypes::get($this->pendingSectionType) === null) {
            return;
        }

        $this->sections[] = [
            '_id' => (string) Str::uuid(),
            'type' => $this->pendingSectionType,
            'data' => SectionTypes::defaults($this->pendingSectionType),
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

    #[On('asset-selected')]
    public function onAssetSelected(string $handle, mixed $value): void
    {
        if (! str_starts_with($handle, 'section_')) {
            return;
        }

        // Handle: section_{_id}_{fieldHandle} or section_{_id}_image_{slotIndex}
        // UUIDs use hyphens, field handles use underscores — split after the UUID portion
        if (! preg_match('/^section_([0-9a-f-]{36})_(.+)$/', $handle, $matches)) {
            return;
        }

        $sectionId = $matches[1];
        $fieldPart = $matches[2];
        $sectionIndex = array_find_key($this->sections, fn ($section): bool => $section['_id'] === $sectionId);

        if ($sectionIndex === null) {
            return;
        }

        // Gallery slot: image_{slotIndex}
        if (preg_match('/^image_(\d+)$/', $fieldPart, $slotMatches)) {
            $images = $this->sections[$sectionIndex]['data']['images'] ?? [];
            if (count($images) < 6) {
                $this->sections[$sectionIndex]['data']['images'][] = $value;
            }

            return;
        }

        $this->sections[$sectionIndex]['data'][$fieldPart] = $value;
    }

    public function save(): void
    {
        resolve(SavePageLayout::class)->handle($this->entry, $this->sections);

        $this->dispatch('notify', message: 'Page layout saved.');
    }

    public function render(): View|Factory
    {
        return view('livewire.entries.partials.page-builder', [
            'sectionTypeLabels' => SectionTypes::labels(),
        ]);
    }
}
