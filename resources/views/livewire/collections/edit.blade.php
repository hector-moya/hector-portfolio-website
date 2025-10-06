<div>
    <div class="mx-auto w-full max-w-3xl">
        <div class="flex flex-col gap-6">
            {{-- Header --}}
            <div>
                <flux:heading size="xl">Edit Collection</flux:heading>
                <flux:text>Update collection settings</flux:text>
            </div>

            {{-- Form Card --}}
            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-800">
                <form wire:submit="save" class="flex flex-col gap-6">
                    {{-- Name --}}
                    <flux:field>
                        <flux:label>Name <span class="text-red-500">*</span></flux:label>
                        <flux:input wire:model.live="form.name" placeholder="Blog Posts" />
                        <flux:error name="form.name" />
                        <flux:text>A descriptive name for your collection</flux:text>
                    </flux:field>

                    {{-- Slug --}}
                    <flux:field>
                        <flux:label>Slug</flux:label>
                        <flux:input wire:model="form.slug" placeholder="blog-posts" />
                        <flux:error name="form.slug" />
                        <flux:text>URL-friendly identifier (auto-generated if left blank)</flux:text>
                    </flux:field>

                    {{-- Description --}}
                    <flux:field>
                        <flux:label>Description</flux:label>
                        <flux:textarea wire:model="form.description" placeholder="A collection of blog articles..." rows="3" />
                        <flux:error name="form.description" />
                    </flux:field>

                    {{-- Blueprint --}}
                    <flux:field>
                        <flux:label>Blueprint</flux:label>
                        <flux:select wire:model="form.blueprint_id">
                            <option value="">Select a blueprint</option>
                            @foreach ($blueprints as $blueprint)
                                <option value="{{ $blueprint->id }}">{{ $blueprint->name }}</option>
                            @endforeach
                        </flux:select>
                        <flux:error name="form.blueprint_id" />
                        <flux:text>The structure that entries in this collection will follow</flux:text>
                    </flux:field>

                    {{-- Status --}}
                    <flux:field>
                        <flux:checkbox wire:model="form.is_active">
                            <flux:label>Active</flux:label>
                        </flux:checkbox>
                        <flux:error name="form.is_active" />
                    </flux:field>

                    {{-- Actions --}}
                    <div class="flex items-center justify-end gap-3 border-t border-neutral-200 pt-6 dark:border-neutral-700">
                        <flux:button wire:navigate href="{{ route('collections.index') }}" variant="ghost">
                            Cancel
                        </flux:button>
                        <flux:button type="submit" variant="primary">
                            Update Collection
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
