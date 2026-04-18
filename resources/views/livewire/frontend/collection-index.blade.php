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
    @elseif($template === 'main')
        <x-templates.index.main
            :collection="$collection"
            :sections="$sections ?? []"
            :assets="$assets ?? collect()"
            :child-entries="$childEntries ?? collect()"
            :child-template="$childTemplate ?? 'card-grid'"
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
