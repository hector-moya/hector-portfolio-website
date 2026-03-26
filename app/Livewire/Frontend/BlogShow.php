<?php

namespace App\Livewire\Frontend;

use App\Models\Asset;
use App\Models\Entry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

class BlogShow extends Component
{
    public Entry $entry;

    public function mount(string $slug): void
    {
        $this->entry = Entry::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with(['elements.field', 'author', 'collection'])
            ->firstOrFail();
    }

    #[Layout('components.layouts.frontend')]
    public function render(): View|Factory
    {
        return view('livewire.frontend.blog-show', [
            'assets' => $this->loadLayoutAssets($this->entry),
            'theme' => $this->entry->collection?->settings['theme'] ?? 'greenpeace',
        ]);
    }

    public function title(): string
    {
        return $this->entry->title;
    }

    private function loadLayoutAssets(Entry $entry): Collection
    {
        if (empty($entry->layout)) {
            return new Collection;
        }

        $assetIds = collect($entry->layout)
            ->flatMap(function (array $section): array {
                return match ($section['type']) {
                    'hero' => [$section['data']['bg_image'] ?? null],
                    'image_text' => [$section['data']['image'] ?? null],
                    'gallery' => $section['data']['images'] ?? [],
                    default => [],
                };
            })
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($assetIds)) {
            return new Collection;
        }

        return Asset::query()->whereIn('id', $assetIds)->get();
    }
}
