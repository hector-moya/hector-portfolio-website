{{-- resources/views/livewire/frontend/collection-index.blade.php --}}
<div>
    @if($isSingle ?? false)
        <x-dynamic-component
            :component="'templates.detail.' . $template"
            :entry="$entry ?? null"
            :sections="$sections ?? []"
            :assets="$assets ?? collect()"
            :theme="$theme"
        />
    @else
        <x-dynamic-component
            :component="'templates.index.' . $template"
            :collection="$collection"
            :entries="$entries"
            :theme="$theme"
        />
    @endif
</div>
