<?php

namespace EscolaLms\ModelFields;

use EscolaLms\ModelFields\Models\Metadata;
use EscolaLms\ModelFields\Policies\MetadataPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Metadata::class => MetadataPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
        if (!$this->app->routesAreCached() && method_exists(Passport::class, 'routes')) {
            Passport::routes();
        }
    }
}
