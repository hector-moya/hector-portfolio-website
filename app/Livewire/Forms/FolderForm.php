<?php

namespace App\Livewire\Forms;

use App\Actions\Folders\CreateFolder;
use App\Actions\Folders\DeleteFolder;
use App\Actions\Folders\UpdateFolder;
use App\Models\Folder;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FolderForm extends Form
{
    #[Validate('required|string|max:255')]
    public ?string $name = '';

    #[Validate('nullable|integer|exists:folders,id')]
    public ?int $parent_id = null;

    public string $newFolderName = '';

    public ?int $currentFolderId = null;

    public ?int $folderId = null;

    public function set(int $folderId): void
    {
        $folder = Folder::query()->findOrFail($folderId);
        $this->name = $folder->name;
        $this->folderId = $folder->id;
        $this->parent_id = $folder->parent_id;
    }

    public function create(): Folder
    {
        $folder = app(CreateFolder::class)->create([
            'name' => $this->name,
            'parent_id' => $this->currentFolderId ?? null,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        Flux::toast(
            heading: 'Folder Created',
            text: "The folder '{$folder->name}' has been created successfully.",
            variant: 'success'
        );

        $this->reset('newFolderName');

        return $folder;

    }

    public function update(int $folderId): Folder
    {
        $this->validate();

        $folder = app(UpdateFolder::class)->update([
            'id' => $folderId,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'updated_by' => auth()->id(),
        ]);

        Flux::toast(
            heading: 'Folder Updated',
            text: "The folder '{$folder->name}' has been updated successfully.",
            variant: 'success'
        );

        $this->reset('name', 'parent_id');

        return $folder;
    }

    public function destroy(int $folderId): void
    {
        app(DeleteFolder::class)->delete($folderId);

        Flux::toast(
            heading: 'Folder Deleted',
            text: 'The folder has been deleted successfully.',
            variant: 'success'
        );
    }

    public function breadcrumbs(): array
    {
        if ($this->currentFolderId === null || $this->currentFolderId === 0) {
            return [];
        }

        $folder = Folder::query()->findOrFail($this->currentFolderId);

        return $folder->ancestors();
    }

    public function currentFolderName(): ?string
    {
        if ($this->currentFolderId === null || $this->currentFolderId === 0) {
            return null;
        }

        $folder = Folder::query()->findOrFail($this->currentFolderId);

        return $folder->name;
    }
}
