<?php

namespace App\Livewire\Blueprints\Components;

use Livewire\Component;

class SectionCard extends Component
{
    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.blueprints.components.section-card');
    }
}
