<?php

namespace App\Livewire\Navigation;

use App\Facades\Navigation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

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
