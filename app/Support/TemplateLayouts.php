<?php

declare(strict_types=1);

namespace App\Support;

final class TemplateLayouts
{
    /**
     * @return array<string, string>
     */
    public static function indexTemplates(): array
    {
        return [
            'card-grid' => 'Card Grid',
            'list' => 'List',
            'magazine' => 'Magazine',
        ];
    }

    public static function defaultIndexTemplate(): string
    {
        return 'card-grid';
    }

    /**
     * @return array<string, string>
     */
    public static function detailTemplates(): array
    {
        return [
            'landing-page' => 'Landing Page',
            'article' => 'Article',
        ];
    }

    public static function defaultDetailTemplate(): string
    {
        return 'landing-page';
    }
}
