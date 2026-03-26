<?php

namespace App\Livewire\Actions\Navigation;

use App\Facades\Navigation;
use App\Models\Navigation as NavigationModel;
use Illuminate\Support\Facades\Gate;

class CreateNavigation
{
    public function create(array $data): NavigationModel
    {
        Gate::authorize('create', NavigationModel::class);

        $navigation = NavigationModel::query()->create($data);

        Navigation::flush();

        return $navigation;
    }
}
