<?php

namespace EscolaLms\ModelFields\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use EscolaLms\Core\Tests\TestCase as CoreTestCase;
use EscolaLms\ModelFields\ModelFieldsServiceProvider;
// use GuzzleHttp\Client;

use EscolaLms\Courses\Tests\Models\User as UserTest;

class TestCase extends CoreTestCase
{
    use DatabaseTransactions;


    protected function setUp(): void
    {
        parent::setUp();
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
        $app['config']->set('auth.providers.users.model', UserTest::class);
        $app['config']->set('passport.client_uuids', true);
    }
}
