<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GlobalSet;
use App\Models\GlobalVariable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class GlobalsService
{
    /**
     * Get all global sets.
     */
    public function sets(): Collection
    {
        return GlobalSet::with(['blueprint', 'variables'])->get();
    }

    /**
     * Get a global set by its handle.
     */
    public function getSet(string $handle): ?GlobalSet
    {
        return GlobalSet::with(['blueprint', 'variables'])
            ->where('handle', $handle)
            ->first();
    }

    /**
     * Get a variable value by its set and handle.
     */
    public function getValue(string $set, string $variable, mixed $default = null): mixed
    {
        return Cache::rememberForever(
            "globals.{$set}.{$variable}",
            fn () => GlobalVariable::query()
                ->whereHas('globalSet', fn ($q) => $q->where('handle', $set))
                ->where('handle', $variable)
                ->first()->value ?? $default
        );
    }

    /**
     * Set a variable value.
     */
    public function setValue(string $set, string $variable, mixed $value): void
    {
        $globalSet = GlobalSet::query()->firstOrCreate(['handle' => $set]);

        GlobalVariable::query()->updateOrCreate(['global_set_id' => $globalSet->id, 'handle' => $variable], ['value' => $value]);

        Cache::forget("globals.{$set}.{$variable}");
    }

    /**
     * Flush the globals cache.
     */
    public function flushCache(): void
    {
        Cache::tags(['globals'])->flush();
    }
}
