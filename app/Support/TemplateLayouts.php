<?php

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
