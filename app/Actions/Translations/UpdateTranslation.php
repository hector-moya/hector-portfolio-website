<?php

namespace App\Actions\Translations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class UpdateTranslation
{
    public function handle(Model $model, string $field, string $locale, string $value): void
    {
        Gate::authorize('update', $model);

        if (method_exists($model, 'setTranslation')) {
            $model->setTranslation($field, $locale, $value);
            $model->save();
        } else {
            $model->{$field} = $value;
            $model->save();
        }
    }
}
