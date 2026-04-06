{{-- resources/views/livewire/frontend/entry-show.blade.php --}}
<div>
    <x-dynamic-component
        :component="'templates.detail.' . $template"
        :entry="$entry"
        :theme="$theme"
    />
</div>
