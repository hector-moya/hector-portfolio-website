<?php

namespace App\Actions\Folders;

use App\Models\Folder;
use Illuminate\Support\Facades\Gate;

class DeleteFolder
{
    public function delete(int $folderId): void
    {
        $folder = Folder::findOrFail($folderId);

        Gate::authorize('delete', $folder);

        $folder->delete();
    }
}
