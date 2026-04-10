<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\GlobalsServiceProvider;
use App\Providers\NavigationServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    GlobalsServiceProvider::class,
    NavigationServiceProvider::class,
];
