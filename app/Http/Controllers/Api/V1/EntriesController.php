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
        $validated = $request->validate([
            'collection' => ['nullable', 'string', 'max:255'],
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $search = isset($validated['search']) ? trim($validated['search']) : null;

        $entries = Entry::query()
            ->where('status', 'published')
            ->with(['collection', 'author', 'elements'])
            ->when($validated['collection'] ?? null, fn ($q, $slug) => $q->whereHas('collection', fn ($c) => $c->where('slug', $slug)))
            ->when($search, fn ($q) => $q->where('title', 'like', '%'.addcslashes($search, '%_\\').'%'))
            ->latest('published_at')
            ->paginate($validated['per_page'] ?? 15);

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
