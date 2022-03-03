<?php

namespace EscolaLms\ModelFields\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use EscolaLms\Core\Tests\TestCase as CoreTestCase;
use EscolaLms\ModelFields\ModelFieldsServiceProvider;
use EscolaLms\Auth\Models\User;
use EscolaLms\ModelFields\Database\Seeders\PermissionTableSeeder;
use EscolaLms\ModelFields\Tests\TestModelFieldsServiceProvider;


class TestCase extends CoreTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionTableSeeder::class);
    }

    protected function getPackageProviders($app): array
    {

        return [
            ...parent::getPackageProviders($app),
            ModelFieldsServiceProvider::class,
            TestModelFieldsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('passport.client_uuids', true);
    }
}
