<div class="flex h-full w-full flex-1 gap-6">
    {{-- Left Sidebar: Topic Navigation --}}
    <div class="w-56 shrink-0">
        <flux:card class="p-4">
            <flux:heading size="sm" class="mb-3 text-zinc-500 dark:text-zinc-400">{{ __('Topics') }}</flux:heading>
            <flux:navlist variant="outline">
                @foreach ($topics as $topic)
                    <flux:navlist.item
                        :icon="$topic['icon']"
                        :href="route('documentation.index', $topic['slug'])"
                        :current="$slug === $topic['slug']"
                        wire:navigate
                    >
                        {{ $topic['title'] }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist>
        </flux:card>
    </div>

    {{-- Main Content --}}
    <div class="flex min-w-0 flex-1 flex-col gap-6">
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Documentation') }}</flux:heading>
                <flux:text>{{ __('Learn how to use the CMS features') }}</flux:text>
            </div>
        </div>

        <flux:card class="prose prose-zinc dark:prose-invert max-w-none p-8">
            @if (view()->exists('livewire.documentation.topics.' . $slug))
                @include('livewire.documentation.topics.' . $slug)
            @else
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <flux:icon.document-text variant="outline" class="mx-auto size-12 text-zinc-400 dark:text-zinc-500" />
                        <flux:heading size="lg" class="mt-4">{{ __('Topic not found') }}</flux:heading>
                        <flux:text class="mt-2 text-zinc-600 dark:text-zinc-400">
                            {{ __('This documentation topic does not exist.') }}
                        </flux:text>
                    </div>
                </div>
            @endif
        </flux:card>
    </div>
</div>
