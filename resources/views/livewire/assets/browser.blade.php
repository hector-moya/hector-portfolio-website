<div class="space-y-4">
    <flux:tab.group>
        <flux:tabs variant="segmented" class="px-4 pt-4">
            <flux:tab name="library">{{ __('Library') }}</flux:tab>
            <flux:tab name="upload">{{ __('Upload') }}</flux:tab>
        </flux:tabs>

        {{-- ── Library tab ── --}}
        <flux:tab.panel name="library" class="px-4 pb-4 space-y-4">

            {{-- Search --}}
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="{{ $mode === 'image' ? __('Search images...') : __('Search files...') }}"
                type="search"
            >
                <x-slot:prefix>
                    <flux:icon name="magnifying-glass" class="h-4 w-4 text-gray-400" />
                </x-slot:prefix>
            </flux:input>

            {{-- Breadcrumbs --}}
            <div class="flex items-center gap-1 text-sm flex-wrap">
                <button type="button" wire:click="openFolder(null)" class="text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200">
                    {{ __('All Files') }}
                </button>
                @foreach ($this->breadcrumbs as $crumb)
                    <flux:icon name="chevron-right" size="micro" class="text-zinc-400" />
                    <button
                        type="button"
                        wire:click="openFolder({{ $crumb['id'] }})"
                        class="text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200"
                    >
                        {{ $crumb['name'] }}
                    </button>
                @endforeach
                @if ($currentFolderId)
                    @php $currentFolder = \App\Models\Folder::find($currentFolderId) @endphp
                    @if ($currentFolder)
                        <flux:icon name="chevron-right" size="micro" class="text-zinc-400" />
                        <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $currentFolder->name }}</span>
                    @endif
                @endif
            </div>

            {{-- Folder list --}}
            @if ($this->folders->isNotEmpty())
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                    @foreach ($this->folders as $folder)
                        <button
                            type="button"
                            wire:click="openFolder({{ $folder->id }})"
                            class="flex items-center gap-2 rounded-lg border px-3 py-2 text-left text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                        >
                            <flux:icon name="folder" size="micro" class="shrink-0 text-zinc-400" />
                            <span class="truncate">{{ $folder->name }}</span>
                        </button>
                    @endforeach
                </div>
                <flux:separator />
            @endif

            {{-- Success notice after upload --}}
            @if ($lastUploadCount > 0)
                <flux:callout variant="success" icon="check-circle">
                    {{ trans_choice(':count file uploaded.|:count files uploaded.', $lastUploadCount, ['count' => $lastUploadCount]) }}
                    {{ __('Select it from the grid below.') }}
                </flux:callout>
            @endif

            {{-- Asset grid (images) --}}
            @if ($mode === 'image')
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 max-h-[50vh] overflow-y-auto">
                    @forelse($assets as $asset)
                        <button
                            type="button"
                            wire:click="selectAsset({{ $asset->id }})"
                            class="group relative aspect-square rounded-lg border-2 border-zinc-200 dark:border-zinc-700 overflow-hidden hover:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
                        >
                            <img
                                src="{{ $asset->thumbnail_url ?? $asset->url }}"
                                alt="{{ $asset->alt_text ?? $asset->original_filename }}"
                                class="w-full h-full object-cover"
                            />
                            <div class="absolute inset-x-0 bottom-0 bg-black/50 px-1 py-0.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <p class="truncate text-xs text-white">{{ $asset->original_filename }}</p>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full py-8 text-center text-sm text-zinc-500">
                            {{ __('No images yet. Upload some in the Upload tab.') }}
                        </div>
                    @endforelse
                </div>

            {{-- Asset list (files) --}}
            @else
                <div class="max-h-[50vh] overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse($assets as $asset)
                        <button
                            type="button"
                            wire:click="selectAsset({{ $asset->id }})"
                            class="flex w-full items-center gap-3 px-2 py-2 text-left hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors"
                        >
                            <flux:icon name="document" size="micro" class="shrink-0 text-zinc-400" />
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm">{{ $asset->original_filename }}</p>
                                <p class="text-xs text-zinc-400">{{ $asset->size_for_humans }}</p>
                            </div>
                        </button>
                    @empty
                        <div class="py-8 text-center text-sm text-zinc-500">
                            {{ __('No files yet. Upload some in the Upload tab.') }}
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- Pagination --}}
            @if ($assets->hasPages())
                <div class="mt-2">
                    {{ $assets->links() }}
                </div>
            @endif

        </flux:tab.panel>

        {{-- ── Upload tab ── --}}
        <flux:tab.panel name="upload" class="px-4 pb-4 space-y-4">

            {{-- Loading overlay: covers the whole tab during temp-upload and processing --}}
            <div
                wire:loading
                wire:target="pendingUploads,uploadFiles"
                class="flex flex-col items-center gap-3 rounded-xl border-2 border-dashed border-teal-300 bg-teal-50 p-8 text-center dark:border-teal-700 dark:bg-teal-900/20"
            >
                <svg class="h-7 w-7 animate-spin text-teal-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-sm font-medium text-teal-700 dark:text-teal-300" wire:loading wire:target="pendingUploads">{{ __('Preparing files…') }}</p>
                <p class="text-sm font-medium text-teal-700 dark:text-teal-300" wire:loading wire:target="uploadFiles">{{ __('Uploading and processing…') }}</p>
            </div>

            {{-- Drop zone + staged list (hidden while loading) --}}
            <div wire:loading.remove wire:target="pendingUploads,uploadFiles">

                <div
                    x-data="{
                        dragging: false,
                        handleDrop(e) {
                            this.dragging = false;
                            const dt = e.dataTransfer;
                            if (!dt?.files?.length) return;
                            const input = $refs.fileInput;
                            const transfer = new DataTransfer();
                            Array.from(dt.files).forEach(f => transfer.items.add(f));
                            input.files = transfer.files;
                            input.dispatchEvent(new Event('change'));
                        }
                    }"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="handleDrop($event)"
                >
                    <label
                        for="browser-file-input-{{ $fieldHandle }}"
                        :class="dragging ? 'border-teal-400 bg-teal-50 dark:bg-teal-900/20' : 'border-zinc-300 dark:border-zinc-600 hover:border-teal-400 hover:bg-zinc-50 dark:hover:bg-zinc-800/50'"
                        class="flex cursor-pointer flex-col items-center gap-3 rounded-xl border-2 border-dashed p-8 transition-colors text-center"
                    >
                        <flux:icon name="arrow-up-tray" class="size-8 text-zinc-400" />
                        <div>
                            <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                {{ __('Drop files here or click to browse') }}
                            </p>
                            <p class="mt-0.5 text-xs text-zinc-400">
                                {{ $mode === 'image' ? __('PNG, JPG, GIF, WebP, SVG') : __('Any file type') }}
                            </p>
                        </div>
                        <input
                            id="browser-file-input-{{ $fieldHandle }}"
                            x-ref="fileInput"
                            type="file"
                            wire:model="pendingUploads"
                            @if ($mode === 'image') accept="image/*" @endif
                            multiple
                            class="sr-only"
                        />
                    </label>
                </div>

                {{-- Staged files list --}}
                @if (count($pendingUploads) > 0)
                    <div class="mt-4 space-y-3">
                        <flux:text size="sm" class="font-medium text-zinc-700 dark:text-zinc-300">
                            {{ trans_choice(':count file ready to upload.|:count files ready to upload.', count($pendingUploads), ['count' => count($pendingUploads)]) }}
                        </flux:text>

                        <div class="max-h-40 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700">
                            @foreach ($pendingUploads as $file)
                                <div class="flex items-center gap-3 px-3 py-2">
                                    <flux:icon name="{{ str_starts_with($file->getMimeType(), 'image/') ? 'photo' : 'document' }}" size="micro" class="shrink-0 text-zinc-400" />
                                    <span class="min-w-0 flex-1 truncate text-sm">{{ $file->getClientOriginalName() }}</span>
                                    <span class="shrink-0 text-xs text-zinc-400">{{ number_format($file->getSize() / 1024, 1) }} KB</span>
                                </div>
                            @endforeach
                        </div>

                        <flux:button
                            type="button"
                            variant="primary"
                            icon="arrow-up-tray"
                            wire:click="uploadFiles"
                            class="w-full"
                        >
                            {{ trans_choice('Upload :count file|Upload :count files', count($pendingUploads), ['count' => count($pendingUploads)]) }}
                        </flux:button>
                    </div>
                @endif

            </div>

        </flux:tab.panel>
    </flux:tab.group>
</div>
