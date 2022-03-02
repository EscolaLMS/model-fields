<?php

namespace EscolaLms\ModelFields\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use EscolaLms\Core\Tests\TestCase as CoreTestCase;
use EscolaLms\ModelFields\ModelFieldsServiceProvider;
// use EscolaLms\ModelFields\Tests\Models\User;
use EscolaLms\Auth\Models\User;
use EscolaLms\ModelFields\Database\Seeders\PermissionTableSeeder;
// use GuzzleHttp\Client;


class TestCase extends CoreTestCase
{
    use DatabaseTransactions;


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionTableSeeder::class);
        // Passport::useClientModel(Client::class);
    }

    protected function getPackageProviders($app): array
    {

        return [
            ...parent::getPackageProviders($app),
            ModelFieldsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.providers.users.model', User::class);
        $app['config']->set('passport.client_uuids', true);
    }
}
