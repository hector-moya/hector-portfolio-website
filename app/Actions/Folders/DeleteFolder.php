<?php

namespace App\Actions\Folders;

use Illuminate\Support\Facades\Gate;

class DeleteFolder
{
    public function delete(int $folderId): void
    {
        $folder = \App\Models\Folder::query()->findOrFail($folderId);

        Gate::authorize('delete', $folder);

        $folder->delete();
    }
}
