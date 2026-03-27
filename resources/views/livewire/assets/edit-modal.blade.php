<div>
    @if ($this->asset)
        <flux:modal name="edit-asset" class="md:w-[36rem]">
            <div class="space-y-6">
                <flux:heading size="lg">{{ __('Edit Asset') }}</flux:heading>

                {{-- Preview --}}
                <div class="flex items-start gap-4">
                    @if ($this->asset->is_image)
                        <img
                            src="{{ $this->asset->thumbnail_url ?? $this->asset->url }}"
                            alt="{{ $this->asset->alt_text ?? $this->asset->original_filename }}"
                            class="h-24 w-24 rounded-lg border object-cover"
                        />
                    @else
                        <div class="flex h-24 w-24 items-center justify-center rounded-lg border bg-zinc-50 dark:bg-zinc-900">
                            <flux:icon name="document" class="h-10 w-10 text-zinc-400" />
                        </div>
                    @endif

                    <div class="min-w-0 flex-1 space-y-1">
                        <flux:text class="truncate font-medium">{{ $this->asset->original_filename }}</flux:text>
                        <flux:text size="sm" class="text-zinc-500">{{ $this->asset->mime_type }} &middot; {{ $this->asset->size_for_humans }}</flux:text>

                        {{-- Copyable URL --}}
                        <div class="flex items-center gap-1 pt-1">
                            <flux:input
                                readonly
                                value="{{ $this->asset->url }}"
                                size="sm"
                                class="font-mono text-xs"
                                x-on:click="$el.select()"
                            />
                            <flux:button
                                size="xs"
                                variant="ghost"
                                icon="clipboard"
                                x-on:click="navigator.clipboard.writeText('{{ $this->asset->url }}')"
                                title="{{ __('Copy URL') }}"
                            />
                        </div>

                        {{-- Usage indicator --}}
                        @if ($this->usageCount > 0)
                            <div class="flex items-center gap-1 pt-1">
                                <flux:icon name="information-circle" size="micro" class="text-blue-500" />
                                <flux:text size="sm" class="text-blue-600 dark:text-blue-400">
                                    {{ trans_choice(':count entry|:count entries', $this->usageCount) }}
                                </flux:text>
                            </div>
                        @endif
                    </div>
                </div>

                <flux:separator />

                {{-- Edit form --}}
                <form wire:submit="save" class="space-y-4">
                    <flux:field>
                        <flux:label>{{ __('Title') }}</flux:label>
                        <flux:input wire:model="title" placeholder="{{ __('Display title') }}" />
                        <flux:error name="title" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Alt Text') }}</flux:label>
                        <flux:input wire:model="alt_text" placeholder="{{ __('Describe the image for screen readers') }}" />
                        <flux:description>{{ __('Used for accessibility and SEO.') }}</flux:description>
                        <flux:error name="alt_text" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Caption') }}</flux:label>
                        <flux:textarea wire:model="caption" rows="2" placeholder="{{ __('Optional caption displayed below the image') }}" />
                        <flux:error name="caption" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Description') }}</flux:label>
                        <flux:textarea wire:model="description" rows="3" placeholder="{{ __('Internal description or notes') }}" />
                        <flux:error name="description" />
                    </flux:field>

                    <flux:field>
                        <flux:label>{{ __('Copyright') }}</flux:label>
                        <flux:input wire:model="copyright" placeholder="{{ __('e.g. © 2025 John Doe') }}" />
                        <flux:error name="copyright" />
                    </flux:field>

                    <div class="flex justify-end gap-2 pt-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    @endif
</div>
