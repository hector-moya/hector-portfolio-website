<div>
    <x-dynamic-component
        :component="'templates.detail.' . $template"
        :entry="$entry"
        :sections="$sections"
        :assets="$assets"
        :theme="$theme"
    />
</div>
