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


            $oldPath = $folder->path;
            $newPath = Folder::makePath($parent, $folderData['name']);

            $folder->update([
                'name' => $folderData['name'],
                'parent_id' => $folderData['parent_id'] ?? null,
                'path' => $newPath,
                'updated_by' => $folderData['updated_by'],
            ]);

            // Cascade update descendant paths if path changed
            if ($oldPath !== $newPath) {
                Folder::where('path', 'like', "$oldPath/%")->get()
                    ->each(function ($child) use ($oldPath, $newPath) {
                        $child->update([
                            'path' => preg_replace("#^{$oldPath}#", $newPath, $child->path),
                        ]);
                    });
            }

            return $folder;
        });
    }
}
