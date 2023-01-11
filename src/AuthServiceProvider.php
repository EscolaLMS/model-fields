<?php

namespace EscolaLms\ModelFields;

use EscolaLms\ModelFields\Models\Metadata;
use EscolaLms\ModelFields\Policies\MetadataPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Metadata::class => MetadataPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
