<div>
    @if ($show && $this->entry)
        <flux:modal wire:model.self="show" class="max-w-4xl">
            <div class="space-y-6">
                {{-- Modal Header --}}
                <div>
                    <flux:heading size="lg">{{ $this->entry->title }}</flux:heading>
                    <flux:text class="mt-2">Preview Entry</flux:text>
                </div>

                {{-- Entry Meta --}}
                <div class="flex items-center gap-4 pb-4 border-b border-zinc-200 dark:border-zinc-700">
                    <div>
                        <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Collection</flux:text>
                        <flux:badge size="sm" color="zinc">{{ $this->entry->collection->name }}</flux:badge>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Status</flux:text>
                        <flux:badge
                            size="sm"
                            :color="match($this->entry->status) {
                                'published' => 'green',
                                'draft' => 'yellow',
                                'archived' => 'zinc',
                            }"
                        >
                            {{ ucfirst($this->entry->status) }}
                        </flux:badge>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Author</flux:text>
                        <flux:text>{{ $this->entry->author->name }}</flux:text>
                    </div>
                    @if ($this->entry->published_at)
                        <div>
                            <flux:text class="text-xs text-zinc-500 dark:text-zinc-400">Published</flux:text>
                            <flux:text>{{ $this->entry->published_at->format('M d, Y') }}</flux:text>
                        </div>
                    @endif
                </div>

                {{-- Entry Content --}}
                <div class="prose prose-zinc dark:prose-invert max-w-none">
                    <h1>{{ $this->entry->title }}</h1>

                    @foreach ($this->entry->elements as $element)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                                {{ $element->blueprintElement->label }}
                            </h3>

                            @switch($element->blueprintElement->type)
                                @case('textarea')
                                @case('richtext')
                                    <div class="whitespace-pre-wrap text-zinc-900 dark:text-zinc-100">
                                        {{ $element->value }}
                                    </div>
                                    @break

                                @case('checkbox')
                                    <div class="text-zinc-900 dark:text-zinc-100">
                                        {{ $element->value ? 'Yes' : 'No' }}
                                    </div>
                                    @break

                                @case('date')
                                    <div class="text-zinc-900 dark:text-zinc-100">
                                        {{ \Carbon\Carbon::parse($element->value)->format('F j, Y') }}
                                    </div>
                                    @break

                                @case('datetime')
                                    <div class="text-zinc-900 dark:text-zinc-100">
                                        {{ \Carbon\Carbon::parse($element->value)->format('F j, Y g:i A') }}
                                    </div>
                                    @break

                                @case('url')
                                    <a href="{{ $element->value }}" target="_blank" class="text-blue-600 hover:underline">
                                        {{ $element->value }}
                                    </a>
                                    @break

                                @case('email')
                                    <a href="mailto:{{ $element->value }}" class="text-blue-600 hover:underline">
                                        {{ $element->value }}
                                    </a>
                                    @break

                                @default
                                    <div class="text-zinc-900 dark:text-zinc-100">
                                        {{ $element->value }}
                                    </div>
                            @endswitch
                        </div>
                    @endforeach
                </div>

                {{-- Modal Footer --}}
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Close</flux:button>
                    </flux:modal.close>
                    <flux:button variant="primary" :href="route('entries.edit', $this->entry)" wire:navigate>
                        Edit Entry
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
