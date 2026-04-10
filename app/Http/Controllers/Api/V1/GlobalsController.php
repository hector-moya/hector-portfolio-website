<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\GlobalSetResource;
use App\Models\GlobalSet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class GlobalsController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $globals = GlobalSet::query()->with('variables')->get();

        return GlobalSetResource::collection($globals);
    }

    public function show(string $handle): GlobalSetResource|JsonResponse
    {
        $global = GlobalSet::query()
            ->where('handle', $handle)
            ->with('variables')
            ->first();

        if (! $global) {
            return response()->json(['message' => 'Global set not found.'], 404);
        }

        return new GlobalSetResource($global);
    }
}
