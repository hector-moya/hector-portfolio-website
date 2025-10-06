<div>
    <flux:header>
        <flux:heading size="xl">Edit Entry</flux:heading>

        <flux:separator />

        <flux:button :href="route('entries')" wire:navigate variant="ghost">
            Cancel
        </flux:button>
    </flux:header>

    <form wire:submit="save" class="space-y-6">
        <flux:card>
            <div class="space-y-6">
                {{-- Collection Info (Read-only) --}}
                <flux:field>
                    <flux:label>Collection</flux:label>
                    <flux:input :value="$entry->collection->name" disabled />
                    <flux:description>Collection cannot be changed after creation</flux:description>
                </flux:field>

                {{-- Basic Entry Fields --}}
                <flux:field>
                    <flux:label>Title</flux:label>
                    <flux:input wire:model.live.debounce.500ms="form.title" required />
                    <flux:error name="form.title" />
                </flux:field>

                <flux:field>
                    <flux:label>Slug</flux:label>
                    <flux:input wire:model="form.slug" required />
                    <flux:description>URL-friendly version of the title</flux:description>
                    <flux:error name="form.slug" />
                </flux:field>

                <div class="grid grid-cols-2 gap-6">
                    <flux:field>
                        <flux:label>Status</flux:label>
                        <flux:select wire:model="form.status" required>
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="archived">Archived</option>
                        </flux:select>
                        <flux:error name="form.status" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Publish Date</flux:label>
                        <flux:input type="datetime-local" wire:model="form.published_at" />
                        <flux:error name="form.published_at" />
                    </flux:field>
                </div>

                {{-- Dynamic Blueprint Fields --}}
                @if ($this->blueprint && $this->blueprint->elements->isNotEmpty())
                    <flux:separator />
                    <flux:heading size="lg">Content Fields</flux:heading>

                    @foreach ($this->blueprint->elements as $element)
                        <flux:field>
                            <flux:label>
                                {{ $element->label }}
                                @if ($element->is_required)
                                    <span class="text-red-500">*</span>
                                @endif
                            </flux:label>

                            @if ($element->instructions)
                                <flux:description>{{ $element->instructions }}</flux:description>
                            @endif

                            @switch($element->type)
                                @case('text')
                                @case('email')
                                @case('url')
                                    <flux:input
                                        type="{{ $element->type }}"
                                        wire:model="form.fieldValues.{{ $element->handle }}"
                                        :required="$element->is_required"
                                    />
                                    @break

                                @case('textarea')
                                    <flux:textarea
                                        wire:model="form.fieldValues.{{ $element->handle }}"
                                        :required="$element->is_required"
                                        rows="4"
                                    />
                                    @break

                                @case('richtext')
                                    <flux:textarea
                                        wire:model="form.fieldValues.{{ $element->handle }}"
                                        :required="$element->is_required"
                                        rows="8"
                                    />
                                    @break

                                @case('number')
                                    <flux:input
                                        type="number"
                                        step="any"
                                        wire:model="form.fieldValues.{{ $element->handle }}"
                                        :required="$element->is_required"
                                    />
                                    @break

                                @case('date')
                                    <flux:input
                                        type="date"
                                        wire:model="form.fieldValues.{{ $element->handle }}"
                                        :required="$element->is_required"
                                    />
                                    @break

                                @case('time')
                                    <flux:input
                                        type="time"
                                        wire:model="form.fieldValues.{{ $element->handle }}"
                                        :required="$element->is_required"
                                    />
                                    @break

                                @case('datetime')
                                    <flux:input
                                        type="datetime-local"
                                        wire:model="form.fieldValues.{{ $element->handle }}"
                                        :required="$element->is_required"
                                    />
                                    @break

                                @case('checkbox')
                                    <flux:checkbox
                                        wire:model="form.fieldValues.{{ $element->handle }}"
                                        :label="$element->label"
                                    />
                                    @break

                                @case('select')
                                    <flux:select
                                        wire:model="form.fieldValues.{{ $element->handle }}"
                                        :required="$element->is_required"
                                    >
                                        <option value="">Select an option...</option>
                                        @if (isset($element->config['options']))
                                            @foreach ($element->config['options'] as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        @endif
                                    </flux:select>
                                    @break

                                @case('radio')
                                    <div class="space-y-2">
                                        @if (isset($element->config['options']))
                                            @foreach ($element->config['options'] as $option)
                                                <flux:radio
                                                    wire:model="form.fieldValues.{{ $element->handle }}"
                                                    value="{{ $option }}"
                                                    :label="$option"
                                                />
                                            @endforeach
                                        @endif
                                    </div>
                                    @break

                                @case('image')
                                @case('file')
                                    <flux:input
                                        type="text"
                                        wire:model="form.fieldValues.{{ $element->handle }}"
                                        :required="$element->is_required"
                                        placeholder="File path or URL"
                                    />
                                    <flux:description>File upload functionality coming soon</flux:description>
                                    @break
                            @endswitch

                            <flux:error name="form.fieldValues.{{ $element->handle }}" />
                        </flux:field>
                    @endforeach
                @endif
            </div>
        </flux:card>

        <div class="flex justify-end gap-2">
            <flux:button type="button" variant="ghost" :href="route('entries')" wire:navigate>
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                Update Entry
            </flux:button>
        </div>
    </form>
</div>
