<div>
    <div class="space-y-4">
        <flux:input wire:model="targetFolder" label="Target Folder" placeholder="Enter destination folder path" />
        <p class="text-sm text-gray-500">
            {{ __('Use forward slashes (/) to specify nested folders. Example: /images/2025/october') }}
        </p>
    </div>

    <x-slot:footer>
        <div class="flex justify-end gap-2">
            <flux:button wire:click="$set('showMoveModal', false)">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button wire:click="move">
                {{ __('Move Asset') }}
            </flux:button>
        </div>
    </x-slot:footer>
</div>
