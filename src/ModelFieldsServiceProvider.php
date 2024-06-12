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
    /**
     * @var string[]
     */
    public array $singletons = [
        ModelFieldsServiceContract::class => ModelFieldsService::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'metadata');

        $this->publishes([
            __DIR__ . '/../config/model-fields.php' => config_path('model-fields.php'),
        ]);
    }

    public function register(): void
    {
        $this->app->register(AuthServiceProvider::class);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/model-fields.php',
            'model-fields'
        );
    }
}
