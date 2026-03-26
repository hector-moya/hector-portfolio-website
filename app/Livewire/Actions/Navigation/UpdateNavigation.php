<?php

namespace App\Livewire\Actions\Navigation;

use App\Facades\Navigation;
use App\Models\Navigation as NavigationModel;
use Illuminate\Support\Facades\Gate;

class UpdateNavigation
{
    public function update(NavigationModel $navigation, array $data): NavigationModel
    {
        Gate::authorize('update', $navigation);

        $navigation->update($data);

        Navigation::flush();

        return $navigation->fresh();
    }
}
