<div>
    <div class="min-h-[32rem] min-w-xs lg:min-w-lg">
        <!-- Toolbar -->
        {{-- <div class="mb-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <!-- Search -->
                <flux:input wire:model.live.debounce.300ms="searchQuery" placeholder="Search assets..." class="w-64">
                    <x-slot:prefix>
                        <flux:icon.magnifying-glass class="h-4 w-4 text-gray-400" />
                    </x-slot:prefix>
                </flux:input>

                <!-- Folder Navigation -->
                <flux:button variant="primary" x-data @click="$dispatch('open-modal', 'create-folder')">
                    <flux:icon.folder-open class="mr-2 h-4 w-4" />
                    {{ __('New Folder') }}
                </flux:button>
            </div>

        </div> --}}
        <!-- Breadcrumb -->
        {{-- <nav class="mb-4 flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm text-gray-500">
                <li>
                    <button type="button" wire:click="navigateToFolder('/')" class="hover:text-gray-700">
                        {{ __('Root') }}
                    </button>
                </li>
                @foreach (explode('/', trim($currentFolder, '/')) as $folder)
                    @if ($folder)
                        <li class="flex items-center">
                            <flux:icon.chevron-double-right class="mx-1 h-4 w-4" />
                            <button type="button" wire:click="navigateToFolder('/{{ $folder }}')" class="hover:text-gray-700">
                                {{ $folder }}
                            </button>
                        </li>
                    @endif
                @endforeach
            </ol>
        </nav> --}}
        <!-- Upload -->
        <div>
            <form wire:submit.prevent="uploadAsset">
                <flux:file-upload wire:model="form.uploadedFiles" multiple label="{{ __('Upload files') }}">
                    <flux:file-upload.dropzone heading="{{ __('Drag & drop files here or click to select') }}" text="{{ __('JPG, PNG, GIF, PDF, DOCX') }}" />
                </flux:file-upload>
                <div class="mt-4 flex flex-col gap-2 max-h-48 overflow-y-auto">
                    @foreach ($form->uploadedFiles as $index => $file)
                        <flux:file-item heading="{{ $file->getClientOriginalName() }}" size="{{ $file->getSize() }}">
                            <x-slot name="actions">
                                <flux:file-item.remove />
                            </x-slot>
                        </flux:file-item>
                    @endforeach
                </div>
                {{-- set the upload button to be at the bottom of the modal --}}
                <div class="absolute bottom-4 right-4">
                    <div class="flex justify-end gap-2">
                        <flux:button type="submit" variant="primary">
                            {{ __('Upload') }}
                        </flux:button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Assets Grid -->
        {{-- <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
            @foreach ($this->assets as $asset)
                <button type="button" wire:click="selectAsset({{ $asset->id }})"
                        class="hover:border-primary-500 focus:ring-primary-500 group relative aspect-square overflow-hidden rounded-lg border-2 border-gray-200 focus:outline-none focus:ring-2">
                    @if ($asset->is_image)
                        <img src="{{ $asset->url }}" alt="{{ $asset->alt_text }}" class="h-full w-full object-cover" />
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-gray-100">
                            <flux:icon.document class="h-8 w-8 text-gray-400" />
                        </div>
                    @endif
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/50 to-transparent p-2">
                        <p class="truncate text-xs text-white">
                            {{ $asset->original_filename }}
                        </p>
                    </div>
                </button>
            @endforeach
        </div> --}}
    </div>

    <!-- Create Folder Modal -->
    {{-- <flux:modal title="Create New Folder" name="create-folder" wire:model="showCreateFolderModal">
        <div class="space-y-4">
            <flux:input wire:model="newFolderName" label="Folder Name" placeholder="Enter folder name" />
        </div>

        <x-slot:footer>
            <div class="flex justify-end gap-2">
                <flux:button variant="primary" x-data @click="$dispatch('close-modal', 'create-folder')">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button variant="primary" wire:click="createFolder">
                    {{ __('Create') }}
                </flux:button>
            </div>
        </x-slot:footer>
    </flux:modal> --}}
</div>
