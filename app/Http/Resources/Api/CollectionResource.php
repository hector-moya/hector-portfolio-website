<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Collection $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'entries_count' => $this->whenCounted('entries'),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
