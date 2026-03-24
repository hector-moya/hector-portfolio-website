<?php

namespace App\Http\Resources\Api;

use App\Models\GlobalSet;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GlobalSetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var GlobalSet $this */
        $variables = [];
        foreach ($this->variables as $variable) {
            $variables[$variable->key] = $variable->value;
        }

        return [
            'id' => $this->id,
            'handle' => $this->handle,
            'name' => $this->name,
            'variables' => $variables,
        ];
    }
}
