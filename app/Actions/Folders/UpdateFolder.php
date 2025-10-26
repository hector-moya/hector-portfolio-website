<?php

namespace App\Actions\Folders;

use App\Models\Folder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UpdateFolder
{
    public function update(array $folderData): Folder
    {
        $folder = Folder::findOrFail($folderData['id']);

        Gate::authorize('update', $folder);

        return DB::transaction(function () use ($folderData, $folder) {
            $parent = Folder::find($folderData['parent_id'] ?? null);

            $folder->update([
                'name' => $folderData['name'],
                'parent_id' => $folderData['parent_id'] ?? null,
                'path' => Folder::makePath($parent, $folderData['name']),
                'updated_by' => $folderData['updated_by'],
            ]);

            return $folder;
        });
    }
}
