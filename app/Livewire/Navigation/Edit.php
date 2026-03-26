<?php

namespace App\Livewire\Navigation;

use App\Facades\Navigation;
use App\Livewire\Forms\NavigationForm;
use App\Models\Navigation as NavigationModel;
use App\Models\NavigationItem;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public NavigationForm $form;

    public NavigationModel $navigation;

    #[Validate(['required', 'string', 'max:255'])]
    public string $newItemTitle = '';

    #[Validate(['required', 'string', 'max:255'])]
    public string $newItemUrl = '';

    public ?NavigationItem $editingItem = null;

    public function mount(NavigationModel $navigation): void
    {
        $this->navigation = $navigation;
        $this->form->setNavigation($navigation);
    }

    public function save(): void
    {
        $this->form->update($this->navigation);
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

    public function render(): View|Factory
    {
        return view('livewire.navigation.edit', [
            'items' => $this->navigation->items()->with('children')->whereNull('parent_id')->orderBy('order')->get(),
        ]);
    }
}
