<?php

declare(strict_types=1);

namespace App\Support;

use App\Facades\Globals;
use App\Models\Entry;
use Illuminate\Support\Str;

final class Seo
{
    /**
     * Build SEO/social metadata for a frontend page from an optional entry,
     * falling back to global branding values when entry-level data is absent.
     *
     * @param  array{title?: string, description?: string, ogImage?: string, ogType?: string}  $defaults
     * @return array{title: ?string, description: ?string, ogImage: ?string, ogType: string}
     */
    public static function forEntry(?Entry $entry, array $defaults = []): array
    {
        $siteName = self::siteName();

        $title = $entry?->seo_title
            ?: ($defaults['title'] ?? null)
            ?: $entry?->title
            ?: $siteName;

        $description = $entry?->seo_description
            ?: $entry?->excerpt
            ?: ($defaults['description'] ?? null)
            ?: self::siteDescription();

        return [
            'title' => $title,
            'description' => $description ? Str::limit(strip_tags($description), 160) : null,
            'ogImage' => $entry?->og_image ?: ($defaults['ogImage'] ?? null),
            'ogType' => $defaults['ogType'] ?? 'website',
        ];
    }

    public static function siteName(): string
    {
        $name = Globals::get('branding.site_name.content', config('app.name'));

        return $name ?: (string) config('app.name');
    }

    public static function siteDescription(): ?string
    {
        $description = Globals::get('branding.site_description.content');

        return $description ?: null;
    }
}
