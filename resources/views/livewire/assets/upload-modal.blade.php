<div>
<flux:modal size="7xl" title="Asset Browser" wire:model="showModal">
    <div class="min-h-[32rem]">
        <!-- Toolbar -->
        <div class="flex items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-4">
                <!-- Search -->
                <flux:input
                    wire:model.live.debounce.300ms="searchQuery"
                    placeholder="Search assets..."
                    class="w-64"
                >
                    <x-slot:prefix>
                        <flux:icon.magnifying-glass class="w-4 h-4 text-gray-400" />
                    </x-slot:prefix>
                </flux:input>

                <!-- Folder Navigation -->
                <flux:button
                    variant="primary"
                    x-data
                    @click="$dispatch('open-modal', 'create-folder')"
                >
                    <flux:icon.folder-open class="w-4 h-4 mr-2" />
                    New Folder
                </flux:button>
            </div>

            <!-- Upload -->
            <div class="flex items-center gap-2">
                <flux:button
                    variant="primary"
                    x-data
                    @click="$refs.fileInput.click()"
                >
                    <flux:icon.arrow-up-tray class="w-4 h-4 mr-2" />
                    Upload
                </flux:button>
                <input
                    type="file"
                    wire:model="upload"
                    class="hidden"
                    x-ref="fileInput"
                    @change="$wire.uploadAsset()"
                >
            </div>
        </div>

        <!-- Breadcrumb -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                <li>
                    <button
                        type="button"
                        wire:click="navigateToFolder('/')"
                        class="hover:text-gray-700"
                    >
                        Root
                    </button>
                </li>
                @foreach(explode('/', trim($currentFolder, '/')) as $folder)
                    @if($folder)
                        <li class="flex items-center">
                            <flux:icon.chevron-double-right class="w-4 h-4 mx-1" />
                            <button
                                type="button"
                                wire:click="navigateToFolder('/{{ $folder }}')"
                                class="hover:text-gray-700"
                            >
                                {{ $folder }}
                            </button>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav>

        <!-- Assets Grid -->
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
            @foreach($assets as $asset)
                <button
                    type="button"
                    wire:click="selectAsset({{ $asset->id }})"
                    class="relative group aspect-square rounded-lg border-2 border-gray-200 overflow-hidden hover:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    @if($asset->is_image)
                        <img
                            src="{{ $asset->url }}"
                            alt="{{ $asset->alt_text }}"
                            class="w-full h-full object-cover"
                        />
                    @else
                        <div class="flex items-center justify-center w-full h-full bg-gray-100">
                            <flux:icon.document class="w-8 h-8 text-gray-400" />
                        </div>
                    @endif
                    <div class="absolute inset-x-0 bottom-0 p-2 bg-gradient-to-t from-black/50 to-transparent">
                        <p class="text-xs text-white truncate">
                            {{ $asset->original_filename }}
                        </p>
                    </div>
                </button>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($assets->hasPages())
            <div class="mt-4">
                {{ $assets->links() }}
            </div>
        @endif
    </div>

    <x-slot:footer>
        <div class="flex justify-end gap-2">
            <flux:button
                variant="primary"
                wire:click="closeModal"
            >
                Cancel
            </flux:button>
            <flux:button
                variant="primary"
                wire:click="selectAsset"
                :disabled="!$selectedAsset"
            >
                Select Asset
            </flux:button>
        </div>
    </x-slot:footer>
</flux:modal>

<!-- Create Folder Modal -->
<flux:modal
    title="Create New Folder"
    name="create-folder"
    wire:model="showCreateFolderModal"
>
    <div class="space-y-4">
        <flux:input
            wire:model="newFolderName"
            label="Folder Name"
            placeholder="Enter folder name"
        />
    </div>

    <x-slot:footer>
        <div class="flex justify-end gap-2">
            <flux:button
                variant="primary"
                x-data
                @click="$dispatch('close-modal', 'create-folder')"
            >
                Cancel
            </flux:button>
            <flux:button
                variant="primary"
                wire:click="createFolder"
            >
                Create
            </flux:button>
        </div>
    </x-slot:footer>
</flux:modal>
