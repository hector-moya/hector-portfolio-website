<div class="space-y-6">
    <flux:heading size="lg">{{ __('Rich Text Field Settings') }}</flux:heading>
    <div class="grid grid-cols-2 gap-4">
        <flux:input label="{{ __('Placeholder') }}" placeholder="{{ __('Eg. Enter text here') }}" wire:model="form.config.placeholder" />
    </div>
    <flux:checkbox.group label="{{ __('Toolbar items') }}" description="{{ __('Select the toolbar items to include in the rich text editor.') }}" wire:model="form.config.toolbar">
        <div class="flex flex-wrap gap-2">
            <flux:checkbox.all label="{{ __('All') }}" />
            @foreach (['heading', 'bold', 'italic', 'strike', 'underline', 'bullet', 'ordered', 'blockquote', 'subscript', 'superscript', 'highlight', 'link', 'code', 'undo', 'redo'] as $tool)
                <flux:checkbox value="{{ $tool }}" label="{{ ucfirst($tool) }}" />
            @endforeach
        </div>
    </flux:checkbox.group>
</div>
