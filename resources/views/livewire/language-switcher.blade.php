<flux:menu.radio.group>
    @foreach ($locales as $code => $name)
        <flux:menu.radio wire:click="switchLocale('{{ $code }}')" :checked="$currentLocale === $code">
            {{ $name }}
        </flux:menu.radio>
    @endforeach
</flux:menu.radio.group>
