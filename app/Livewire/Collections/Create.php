<?php

declare(strict_types=1);

namespace App\Livewire\Collections;

use App\Livewire\Forms\CollectionForm;
use App\Models\Blueprint;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

final class Create extends Component
{
    public CollectionForm $form;

    public function save(): void
    {
        $this->form->create();

        $this->redirect(route('collections.index'), navigate: true);
    }

    #[Title('Create Collection')]
    public function render(): View|Factory
    {
        return view('livewire.collections.create', [
            'blueprints' => Blueprint::query()->where('is_active', true)->get(),
        ]);
    }
}
