<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Entry;
use Illuminate\Http\Response;

final class SitemapController extends Controller
{
    /**
     * Render an XML sitemap of the public, published frontend URLs.
     */
    public function __invoke(): Response
    {
        $urls = [];

        $urls[] = [
            'loc' => url('/'),
            'lastmod' => null,
        ];

        Collection::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['slug', 'updated_at'])
            ->each(function (Collection $collection) use (&$urls): void {
                $urls[] = [
                    'loc' => route('collection.index', $collection->slug),
                    'lastmod' => $collection->updated_at?->toAtomString(),
                ];
            });

        Entry::query()
            ->where('status', 'published')
            ->where('slug', '!=', 'home')
            ->with('collection:id,slug,blueprint_id')
            ->get()
            ->each(function (Entry $entry) use (&$urls): void {
                $collectionSlug = $entry->collection?->slug;

                if ($collectionSlug === null) {
                    return;
                }

                $urls[] = [
                    'loc' => route('entry.show', [$collectionSlug, $entry->slug]),
                    'lastmod' => ($entry->updated_at ?? $entry->published_at)?->toAtomString(),
                ];
            });

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
