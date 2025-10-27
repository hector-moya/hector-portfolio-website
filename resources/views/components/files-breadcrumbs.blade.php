<flux:breadcrumbs>
    <flux:breadcrumbs.item>
        <button wire:click="openFolder(null)" class="hover:underline">{{ __('Root') }}</button>
    </flux:breadcrumbs.item>
    @foreach ($this->breadcrumbs as $crumb)
        <flux:breadcrumbs.item>
            <button wire:click="openFolder({{ $crumb->id }})" class="hover:underline">{{ $crumb->name }}</button>
        </flux:breadcrumbs.item>
    @endforeach
</flux:breadcrumbs>
