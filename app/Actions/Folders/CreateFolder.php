<?php

namespace App\Actions\Folders;

use App\Models\Folder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CreateFolder
{
    public function create(array $folderData): Folder
    {
        Gate::authorize('create', Folder::class);

        return DB::transaction(function () use ($folderData) {
            $parent = Folder::find($folderData['parent_id'] ?? null);

            return Folder::create([
                'name' => $folderData['name'],
                'parent_id' => $folderData['parent_id'] ?? null,
                'path' => Folder::makePath($parent, $folderData['name']),
                'created_by' => $folderData['created_by'],
                'updated_by' => $folderData['updated_by'],
            ]);
        });
    }
}
