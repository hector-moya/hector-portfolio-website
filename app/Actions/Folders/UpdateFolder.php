<?php

declare(strict_types=1);

namespace App\Actions\Folders;

use App\Models\Folder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class UpdateFolder
{
    public function update(array $folderData): Folder
    {
        $folder = Folder::query()->findOrFail($folderData['id']);

        Gate::authorize('update', $folder);

        return DB::transaction(function () use ($folderData, $folder) {
            $parent = Folder::query()->find($folderData['parent_id'] ?? null);

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
                Folder::query()->where('path', 'like', "$oldPath/%")->get()
                    ->each(function ($child) use ($oldPath, $newPath): void {
                        $child->update([
                            'path' => preg_replace("#^{$oldPath}#", $newPath, (string) $child->path),
                        ]);
                    });
            }

            return $folder;
        });
    }
}
