<?php

namespace App\Livewire\Forms;

use App\Livewire\Actions\Taxonomies\CreateTaxonomy;
use App\Livewire\Actions\Taxonomies\DeleteTaxonomy;
use App\Livewire\Actions\Taxonomies\UpdateTaxonomy;
use App\Models\Taxonomy;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TaxonomyForm extends Form
{
    public ?int $taxonomy_id = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate]
    public string $handle = '';

    #[Validate('boolean')]
    public bool $hierarchical = false;

    #[Validate('boolean')]
    public bool $single_select = false;

    public array $terms = [];

    public function rules(): array
    {
        return [
            'handle' => [
                'required',
                'string',
                'max:255',
                Rule::unique('taxonomies', 'handle')->ignore($this->taxonomy_id),
            ],
        ];
    }

    public function setTaxonomy(Taxonomy $taxonomy): void
    {
        $this->taxonomy_id = $taxonomy->id;
        $this->name = $taxonomy->name;
        $this->handle = $taxonomy->handle;
        $this->hierarchical = $taxonomy->hierarchical;
        $this->single_select = $taxonomy->single_select;
        $this->terms = $taxonomy->terms()->get()->toArray();
    }

    public function create(): Taxonomy
    {
        $this->validate();

        $taxonomy = app(CreateTaxonomy::class)->create([
            'name' => $this->name,
            'handle' => $this->handle,
            'hierarchical' => $this->hierarchical,
            'single_select' => $this->single_select,
        ]);

        Flux::toast(
            heading: 'Taxonomy Created',
            text: 'The taxonomy has been successfully created.',
            variant: 'success',
        );

        $this->reset();

        return $taxonomy;
    }

    public function update(int $taxonomyId): Taxonomy
    {
        $this->validate();

        $taxonomy = app(UpdateTaxonomy::class)->update([
            'id' => $taxonomyId,
            'name' => $this->name,
            'handle' => $this->handle,
            'hierarchical' => $this->hierarchical,
            'single_select' => $this->single_select,
        ]);

        Flux::toast(
            heading: 'Taxonomy Updated',
            text: 'The taxonomy has been successfully updated.',
            variant: 'success',
        );

        return $taxonomy;
    }

    public function destroy(int $taxonomyId): void
    {
        $taxonomy = Taxonomy::query()->findOrFail($taxonomyId);

        app(DeleteTaxonomy::class)->execute($taxonomy);

        Flux::toast(
            heading: 'Taxonomy Deleted',
            text: 'The taxonomy has been successfully deleted.',
            variant: 'success',
        );
    }

    public function generateHandle(string $name): string
    {
        return Str::slug($name);
    }

    public function updatedName(): void
    {
        $this->handle = $this->generateHandle($this->name);
    }
}
