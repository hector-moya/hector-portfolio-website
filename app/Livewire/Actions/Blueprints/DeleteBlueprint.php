<?php

namespace App\Livewire\Actions\Blueprints;

use App\Models\Blueprint;

class DeleteBlueprint
{
    public function execute(Blueprint $blueprint): bool
    {
        return $blueprint->delete();
    }
}
