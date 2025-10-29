<?php

namespace App\View\Components;

use App\Models\Navigation;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class Menu extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $handle,
        public bool $cache = true,
        public ?string $class = null
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        $navigation = Cache::remember(
            "navigation.{$this->handle}",
            now()->addHour(),
            fn () => Navigation::where('handle', $this->handle)
                ->with('items.linkable')
                ->first()
        );

        return view('components.menu', [
            'navigation' => $navigation,
            'class' => $this->class,
        ]);
    }
}
