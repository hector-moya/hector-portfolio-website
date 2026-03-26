<?php

namespace App\Livewire\Forms;

use App\Livewire\Actions\Collections\CreateCollection;
use App\Livewire\Actions\Collections\DeleteCollection;
use App\Livewire\Actions\Collections\UpdateCollection;
use App\Models\Collection;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CollectionForm extends Form
{
    public ?int $collection_id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    public string $slug = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('nullable|exists:blueprints,id')]
    public ?int $blueprint_id = null;

    #[Validate('boolean')]
    public bool $is_active = true;

    public function rules(): array
    {
        return [
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('collections', 'slug')
                    ->ignore($this->collection_id),
            ],
        ];
    }

    public function setCollection($collection): void
    {
        $this->collection_id = $collection->id;
        $this->name = $collection->name;
        $this->slug = $collection->slug;
        $this->description = $collection->description ?? '';
        $this->blueprint_id = $collection->blueprint_id;
        $this->is_active = $collection->is_active;
    }

    public function create(): Collection
    {
        $this->validate();

        $collection = app(CreateCollection::class)->execute([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'blueprint_id' => $this->blueprint_id,
            'is_active' => $this->is_active,
        ]);

        Flux::toast(
            heading: 'Collection Created',
            text: 'The collection has been successfully created.',
            variant: 'success',
        );

        $this->reset('name', 'slug', 'description', 'blueprint_id', 'is_active');

        return $collection;
    }

    public function update(Collection $collection): Collection
    {
        $this->validate();

        $updated = app(UpdateCollection::class)->execute($collection, [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'blueprint_id' => $this->blueprint_id,
            'is_active' => $this->is_active,
        ]);

        Flux::toast(
            heading: 'Collection Updated',
            text: 'The collection has been successfully updated.',
            variant: 'success',
        );

        return $updated;
    }

    public function destroy(int $collectionId): void
    {
        $collection = Collection::query()->findOrFail($collectionId);

        app(DeleteCollection::class)->execute($collection);

        Flux::toast(
            heading: 'Collection Deleted',
            text: 'The collection has been successfully deleted.',
            variant: 'success',
        );
    }
}
