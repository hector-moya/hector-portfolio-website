<?php

namespace App\Livewire\Navigation;

use App\Facades\Navigation;
use App\Models\Navigation as NavigationModel;
use App\Models\NavigationItem;
use Illuminate\Support\Str;
use Livewire\Component;

class Edit extends Component
{
    public NavigationModel $navigation;

    public array $form = [
        'name' => '',
        'handle' => '',
        'description' => '',
        'is_active' => true,
    ];

    #[\Livewire\Attributes\Validate(['required', 'string', 'max:255'])]
    public string $newItemTitle = '';

    #[\Livewire\Attributes\Validate(['required', 'string', 'max:255'])]
    public string $newItemUrl = '';

    public ?NavigationItem $editingItem = null;

    public function mount(NavigationModel $navigation): void
    {
        $this->navigation = $navigation;
        $this->form = $navigation->toArray();
    }

    public function updatedFormName($value): void
    {
        if ($this->form['handle'] === $this->navigation->handle) {
            $this->form['handle'] = Str::slug($value);
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'form.name' => ['required', 'string', 'max:255'],
            'form.handle' => ['required', 'string', 'max:255', "unique:navigations,handle,{$this->navigation->id}"],
            'form.description' => ['nullable', 'string', 'max:500'],
            'form.is_active' => ['required', 'boolean'],
        ]);

        $this->navigation->update($validated['form']);

        Navigation::flush();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Navigation updated successfully.'),
        ]);
    }

    public function addItem(): void
    {
        $this->validateOnly('newItemTitle');
        $this->validateOnly('newItemUrl');

        $this->navigation->items()->create([
            'title' => $this->newItemTitle,
            'url' => $this->newItemUrl,
            'order' => $this->navigation->items()->count(),
        ]);

        Navigation::flush();

        $this->newItemTitle = '';
        $this->newItemUrl = '';

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Item added successfully.'),
        ]);
    }

    public function editItem(NavigationItem $item): void
    {
        $this->editingItem = $item;
    }

    public function updateItem(): void
    {
        $this->validateOnly('editingItem.title', ['editingItem.title' => ['required', 'string', 'max:255']]);
        $this->validateOnly('editingItem.url', ['editingItem.url' => ['required', 'string', 'max:255']]);

        $this->editingItem->save();

        Navigation::flush();

        $this->editingItem = null;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Item updated successfully.'),
        ]);
    }

    public function deleteItem(NavigationItem $item): void
    {
        $item->delete();

        Navigation::flush();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Item deleted successfully.'),
        ]);
    }

    public function reorder($items): void
    {
        Navigation::reorder($items);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Items reordered successfully.'),
        ]);
    }

    public function render(): \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
    {
        return view('livewire.navigation.edit', [
            'items' => $this->navigation->items()->with('children')->whereNull('parent_id')->orderBy('order')->get(),
        ]);
    }
}
