<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class EntryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Entry $this */
        $fields = [];
        foreach ($this->elements as $element) {
            if (isset($element->meta['parent_handle'])) {
                continue;
            }

            $fields[$element->handle] = $element->getElementValue();
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'collection' => $this->whenLoaded('collection', fn (): array => [
                'id' => $this->collection?->id,
                'name' => $this->collection?->name,
                'slug' => $this->collection?->slug,
            ]),
            'author' => $this->whenLoaded('author', fn (): array => [
                'id' => $this->author?->id,
                'name' => $this->author?->name,
            ]),
            'seo' => [
                'title' => $this->seo_title ?: $this->title,
                'description' => $this->seo_description,
                'og_image' => $this->og_image,
            ],
            'fields' => $fields,
        ];
    }
}
