<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\EntryResource;
use App\Models\Entry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class EntriesController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $entries = Entry::query()
            ->where('status', 'published')
            ->with(['collection', 'author', 'elements'])
            ->when($request->collection, fn ($q) => $q->whereHas('collection', fn ($c) => $c->where('slug', $request->collection)))
            ->when($request->search, fn ($q) => $q->where('title', 'like', sprintf('%%%s%%', $request->search)))
            ->latest('published_at')
            ->paginate($request->integer('per_page', 15));

        return EntryResource::collection($entries);
    }

    public function show(string $slug): EntryResource|JsonResponse
    {
        $entry = Entry::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->with(['collection', 'author', 'elements'])
            ->first();

        if (! $entry) {
            return response()->json(['message' => 'Entry not found.'], 404);
        }

        return new EntryResource($entry);
    }
}
