<?php

namespace App\Livewire\Navigation;

use App\Facades\Navigation;
use App\Models\Navigation as NavigationModel;
use Livewire\Component;
use Illuminate\Support\Str;

class Create extends Component
{
    public array $form = [
        'name' => '',
        'handle' => '',
        'description' => '',
        'is_active' => true,
    ];

    public function updatedFormName($value)
    {
        $this->form['handle'] = Str::slug($value);
    }

    public function save()
    {
        $validated = $this->validate([
            'form.name' => ['required', 'string', 'max:255'],
            'form.handle' => ['required', 'string', 'max:255', 'unique:navigations,handle'],
            'form.description' => ['nullable', 'string', 'max:500'],
            'form.is_active' => ['required', 'boolean'],
        ]);

        $navigation = NavigationModel::create($validated['form']);

        Navigation::flush();

        $this->redirect(route('navigation.edit', $navigation), navigate: true);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('Navigation created successfully.'),
        ]);
    }

    public function render()
    {
        return view('livewire.navigation.create');
    }
}
