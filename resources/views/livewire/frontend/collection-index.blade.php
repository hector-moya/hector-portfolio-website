{{-- resources/views/livewire/frontend/collection-index.blade.php --}}
<div>
    <x-dynamic-component
        :component="'templates.index.' . $template"
        :collection="$collection"
        :entries="$entries"
        :theme="$theme"
    />
</div>
