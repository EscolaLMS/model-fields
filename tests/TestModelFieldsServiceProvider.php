<?php

namespace EscolaLms\ModelFields\Tests;

use Illuminate\Support\ServiceProvider;

class TestModelFieldsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/test_routes.php');
    }
}
