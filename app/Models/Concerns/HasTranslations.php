<?php

namespace App\Models\Concerns;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

trait HasTranslations
{
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function translate(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? App::getLocale();

        if ($locale === config('app.fallback_locale')) {
            return $this->getAttribute($field);
        }

        return $this->translations()
            ->where('field', $field)
            ->where('locale', $locale)
            ->value('value') ?? $this->getAttribute($field);
    }

    public function setTranslation(string $field, string $locale, string $value): self
    {
        $this->translations()->updateOrCreate(
            [
                'field' => $field,
                'locale' => $locale,
            ],
            [
                'value' => $value,
            ]
        );

        return $this;
    }

    public function getTranslations(string $field): array
    {
        $translations = $this->translations()
            ->where('field', $field)
            ->pluck('value', 'locale')
            ->toArray();

        // Add the default language value
        $translations[config('app.fallback_locale')] = $this->getAttribute($field);

        return $translations;
    }
}
