<div>
    <flux:dropdown position="bottom" align="start">
        <flux:button variant="ghost" size="sm">
            <div class="flex items-center gap-2">
                <span class="font-medium">{{ $locales[$currentLocale] }}</span>
                <flux:icon name="chevron-down" class="h-4 w-4" />
            </div>
        </flux:button>

        <flux:menu class="w-48">
            @foreach($locales as $code => $name)
                <flux:menu.item
                    wire:click="switchLocale('{{ $code }}')"
                    :active="$currentLocale === $code"
                >
                    {{ $name }}
                </flux:menu.item>
            @endforeach
        </flux:menu>
    </flux:dropdown>
</div>
