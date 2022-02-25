<?php

namespace EscolaLms\ModelFields;

use Illuminate\Support\ServiceProvider;

/**
 * SWAGGER_VERSION
 */

class ModelFieldsServiceProvider extends ServiceProvider
{


    public $singletons = [
        // JitsiServiceContract::class => JitsiService::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }


    public function register()
    {
    }
}
