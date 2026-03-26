<?php

declare(strict_types=1);

namespace App\Actions\Globals;

use App\Models\GlobalSet;
use Illuminate\Support\Facades\Gate;

class DeleteGlobal
{
    public function handle(int $globalSetId): void
    {
        $globalSet = GlobalSet::query()->findOrFail($globalSetId);

        Gate::authorize('delete', $globalSet);

        $globalSet->delete();
    }
}
