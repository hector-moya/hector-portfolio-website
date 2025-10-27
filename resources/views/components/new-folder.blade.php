<form wire:submit.prevent="createFolder" class="space-y-6">
    <flux:heading size="md">{{ __('Create New Folder') }}</flux:heading>
    <flux:input wire:model="folderForm.name" size="sm" placeholder="{{ __('Folder name') }}" />
    <div class="absolute bottom-4 right-4">
        <div class="mt-4 flex justify-end">
            <flux:button type="submit" variant="primary">{{ __('Create') }}</flux:button>
        </div>
    </div>
</form>
