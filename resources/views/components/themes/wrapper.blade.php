@props(['theme' => 'greenpeace'])
<x-dynamic-component :component="'themes.' . $theme" {{ $attributes }}>
    {{ $slot }}
</x-dynamic-component>
