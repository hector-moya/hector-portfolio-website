<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Livewire\Frontend\Concerns\ResolvesFrontendAssets;
use App\Models\Entry;
use App\Support\Seo;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

final class Home extends Component
{
    use ResolvesFrontendAssets;

    #[Layout('components.layouts.frontend')]
    public function render(): View|Factory
    {
        $entry = Entry::query()
            ->where('slug', 'home')
            ->where('status', 'published')
            ->with(['elements.field', 'collection'])
            ->first();

        $sections = $entry?->getPageBuilderSections() ?? [];
        $assets = $this->resolveAssets($sections);
        $theme = $entry?->collection?->settings['theme'] ?? 'greenpeace';

        $seo = Seo::forEntry($entry, ['title' => Seo::siteName()]);

        return view('livewire.frontend.home', [
            'entry' => $entry,
            'layout' => $sections,
            'assets' => $assets,
            'theme' => $theme,
        ])
            ->title($seo['title'])
            ->layoutData([
                'description' => $seo['description'],
                'ogImage' => $seo['ogImage'],
                'ogType' => $seo['ogType'],
            ]);
    }
}
