<?php

namespace App\Livewire\Actions\Collections;

use App\Models\Collection;
use Illuminate\Support\Str;

// TODO: Two issues here:
//   1. No authorization — add Gate::authorize('create', Collection::class) before the DB work.
//      Compare with CreateTaxonomy, CreateUser, and CreateGlobal which all check Gate.
//   2. Method is named execute() while similar actions use create() (CreateUser, CreateTaxonomy, CreateFolder)
//      or handle() (CreateEntry, CreateGlobal). Standardize all action entry-point methods to handle()
//      across the entire codebase (app/Livewire/Actions/ and app/Actions/) for consistency.
class CreateCollection
{
    public function execute(array $data): Collection
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return \App\Models\Collection::query()->create($data);
    }
}
