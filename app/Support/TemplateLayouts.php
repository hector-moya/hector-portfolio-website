<?php

declare(strict_types=1);

namespace App\Support;

class TemplateLayouts
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

    /**
     * @return array<string, string>
     */
    public static function detailTemplates(): array
    {
        return [
            'article' => 'Article',
            'full-width' => 'Full Width',
            'minimal' => 'Minimal',
            'landing-page' => 'Landing Page',
        ];
    }

    public static function defaultIndexTemplate(): string
    {
        return 'card-grid';
    }

    public static function defaultDetailTemplate(): string
    {
        return 'article';
    }
}
