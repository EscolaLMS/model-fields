<?php

namespace EscolaLms\ModelFields;

use Illuminate\Support\ServiceProvider;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;
use EscolaLms\ModelFields\Services\ModelFieldsService;

/**
 * SWAGGER_VERSION
 */

class ModelFieldsServiceProvider extends ServiceProvider
{
    public $singletons = [
        ModelFieldsServiceContract::class => ModelFieldsService::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }

    public function register()
    {
        $this->app->register(AuthServiceProvider::class);
    }
}