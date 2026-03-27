<?php

namespace App\Livewire\Frontend;

use App\Models\Asset;
use App\Models\Entry;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Home extends Component
{
    #[Layout('components.layouts.frontend')]
    #[Title('Home')]
    public function render(): View|Factory
    {
        $entry = Entry::query()
            ->where('slug', 'home')
            ->where('status', 'published')
            ->with(['elements.field', 'collection'])
            ->first();

        $sections = $entry?->getPageBuilderSections() ?? [];
        $assets = $this->loadLayoutAssets($entry, $sections);
        $theme = $entry?->collection?->settings['theme'] ?? 'greenpeace';

        return view('livewire.frontend.home', [
            'entry' => $entry,
            'layout' => $sections,
            'assets' => $assets,
            'theme' => $theme,
        ]);
    }

    private function loadLayoutAssets(?Entry $entry, array $sections): Collection
    {
        if (! $entry || empty($sections)) {
            return new Collection;
        }

        $assetIds = collect($sections)
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
