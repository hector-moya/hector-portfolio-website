@props([
    'folderId' => null,
    'name' => '',
])
<form wire:submit.prevent="renameFolder({{ $folderId }})" class="mt-2">
    <div class="space-y-6">
        <flux:heading size="lg">{{ __('Rename Folder') }}</flux:heading>
        <flux:input wire:model="name" placeholder="{{ __('Folder name') }}" />
    </div>
    <div class="absolute bottom-4 right-4">
        <div class="mt-4 flex justify-end">
            <flux:button type="submit" variant="primary">{{ __('Rename') }}</flux:button>
        </div>
    </div>
</form>
