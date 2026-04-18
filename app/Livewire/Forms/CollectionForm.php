<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Livewire\Actions\Collections\CreateCollection;
use App\Livewire\Actions\Collections\DeleteCollection;
use App\Livewire\Actions\Collections\UpdateCollection;
use App\Models\Collection;
use Flux\Flux;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

final class CollectionForm extends Form
{
    public ?int $collection_id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    public string $slug = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    #[Validate('nullable|exists:blueprints,id')]
    public ?int $blueprint_id = null;

    #[Validate('nullable|exists:collections,id')]
    public ?int $parent_id = null;

    #[Validate('boolean')]
    public bool $is_active = true;

    #[Validate('nullable|string|max:255')]
    public string $theme = '';

    #[Validate('nullable|string')]
    public string $index_template = '';

    #[Validate('required|string|in:standard,single,main,child')]
    public string $type = 'standard';

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

    public function setCollection(Collection $collection): void
    {
        $this->collection_id = $collection->id;
        $this->name = $collection->name;
        $this->slug = $collection->slug;
        $this->description = $collection->description ?? '';
        $this->blueprint_id = $collection->blueprint_id;
        $this->parent_id = $collection->parent_id;
        $this->is_active = $collection->is_active;
        $this->theme = $collection->settings['theme'] ?? '';
        $this->index_template = $collection->settings['index_template'] ?? '';
        $this->type = $collection->settings['type'] ?? 'standard';
    }

    public function create(): Collection
    {
        $this->validate();

        $collection = resolve(CreateCollection::class)->execute([
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'blueprint_id' => $this->blueprint_id,
            'parent_id' => $this->parent_id,
            'is_active' => $this->is_active,
            'settings' => array_filter([
                'theme' => $this->theme ?: null,
                'index_template' => $this->index_template ?: null,
                'type' => $this->type !== 'standard' ? $this->type : null,
            ]),
        ]);

        Flux::toast(
            heading: 'Collection Created',
            text: 'The collection has been successfully created.',
            variant: 'success',
        );

        $this->reset('name', 'slug', 'description', 'blueprint_id', 'parent_id', 'is_active', 'theme', 'index_template', 'type');

        return $collection;
    }

    public function update(Collection $collection): Collection
    {
        $this->validate();

        $updated = resolve(UpdateCollection::class)->execute($collection, [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'blueprint_id' => $this->blueprint_id,
            'parent_id' => $this->parent_id,
            'is_active' => $this->is_active,
            'settings' => array_filter(array_merge($collection->settings ?? [], [
                'theme' => $this->theme ?: null,
                'index_template' => in_array($this->type, ['single', 'child']) ? null : ($this->index_template ?: null),
                'type' => $this->type !== 'standard' ? $this->type : null,
            ])),
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

        resolve(DeleteCollection::class)->execute($collection);

        Flux::toast(
            heading: 'Collection Deleted',
            text: 'The collection has been successfully deleted.',
            variant: 'success',
        );
    }
}
