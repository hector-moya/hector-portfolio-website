<div>
    <div class="space-y-4">
        @foreach($locales as $code => $name)
        <div>
            <flux:input
                :label="$name"
                wire:model="translations.{{ $code }}"
                wire:change="updateTranslation('{{ $code }}', $event.target.value)"
            />
        </div>
        @endforeach
    </div>
</div>
