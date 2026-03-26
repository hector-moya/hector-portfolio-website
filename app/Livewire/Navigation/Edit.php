<?php

namespace App\Livewire\Navigation;

use App\Facades\Navigation;
use App\Models\Navigation as NavigationModel;
use App\Models\NavigationItem;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

// TODO: Replace the $form array with a NavigationForm (extends Livewire\Form) using #[Validate] attributes
//       and a rules() method that includes the unique-with-ignore rule for handle.
//       Add setNavigation(Navigation $navigation) to populate the form on mount.
//       Add an update() method to NavigationForm that calls an UpdateNavigation action.
//       The action should handle Gate authorization, DB transaction, model update, and Navigation::flush().
//
// TODO: Extract $newItemTitle / $newItemUrl and the addItem() / updateItem() / deleteItem() methods
//       into a NavigationItemForm with create(), update(), and destroy() methods that call corresponding
//       actions: CreateNavigationItem, UpdateNavigationItem, DeleteNavigationItem — each responsible for
//       item persistence and dispatching Navigation::flush(). This removes all controller-like logic from
//       the component and aligns with the pattern in Users/Edit.php and Entries/Edit.php.
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

    public function render(): View|Factory
    {
        return view('livewire.navigation.edit', [
            'items' => $this->navigation->items()->with('children')->whereNull('parent_id')->orderBy('order')->get(),
        ]);
    }
}
