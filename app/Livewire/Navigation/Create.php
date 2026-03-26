<?php

namespace App\Livewire\Navigation;

use App\Facades\Navigation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

// TODO: Replace the $form array with a NavigationForm (extends Livewire\Form) that holds name, handle,
//       description, and is_active with #[Validate] attributes and a rules() method for the unique handle check.
//       Move the auto-slug logic into an updatedName() watcher inside the form.
//       Add a create() method to NavigationForm that calls a CreateNavigation action (app/Livewire/Actions/Navigation/CreateNavigation.php).
//       The action should handle Gate authorization, DB transaction, model creation, and Navigation::flush().
//       This component's save() should then just call $this->form->create() and redirect, matching the pattern
//       used in Users/Create.php and Collections/Create.php.
class Create extends Component
{
    public array $form = [
        'name' => '',
        'handle' => '',
        'description' => '',
        'is_active' => true,
    ];

    public function updatedFormName($value): void
    {
        $this->form['handle'] = Str::slug($value);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'form.name' => ['required', 'string', 'max:255'],
            'form.handle' => ['required', 'string', 'max:255', 'unique:navigations,handle'],
            'form.description' => ['nullable', 'string', 'max:500'],
            'form.is_active' => ['required', 'boolean'],
        ]);

        $navigation = \App\Models\Navigation::query()->create($validated['form']);

        Navigation::flush();

        $this->redirect(route('navigation.edit', $navigation), navigate: true);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Navigation created successfully.'),
        ]);
    }

    public function render(): View|Factory
    {
        return view('livewire.navigation.create');
    }
}
