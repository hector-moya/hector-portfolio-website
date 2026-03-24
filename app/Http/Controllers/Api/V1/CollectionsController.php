<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CollectionResource;
use App\Models\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CollectionsController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $collections = Collection::query()
            ->withCount('entries')
            ->orderBy('name')
            ->get();

        return CollectionResource::collection($collections);
    }
}
