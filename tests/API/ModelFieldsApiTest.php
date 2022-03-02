<?php

namespace EscolaLms\ModelFields\Tests\API;

use BadMethodCallException;
use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\ModelFields\Tests\TestCase;
use EscolaLms\ModelFields\Enum\MetaFieldTypeEnum;

use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EscolaLms\Core\Enums\UserRole;
use EscolaLms\ModelFields\Tests\Models\User;
use Illuminate\Support\Facades\App;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;


class ModelFieldsApiTest extends TestCase
{

    use CreatesUsers;

    private ModelFieldsServiceContract $service;
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = config('auth.providers.users.model')::factory()->create();
        $this->user->guard_name = 'api';
        $this->user->assignRole(UserRole::ADMIN);

        $this->service = App::make(ModelFieldsServiceContract::class);

        $this->service->addOrUpdateMetadataField(
            User::class,
            'description',
            'text',
            'lorem ipsum',
            ['required', 'string', 'max:255']
        );

        $this->service->addOrUpdateMetadataField(
            User::class,
            'interested_in_tests',
            'boolean',
            true,
            ['required', 'boolean']
        );

        $this->service->addOrUpdateMetadataField(
            User::class,
            'title',
            'varchar',
            '',
            ['required', 'string', 'max:255']
        );

        $this->service->addOrUpdateMetadataField(
            User::class,
            'consents',
            'json',
            '[]',
            ['required', 'json']
        );
    }

    public function testList()
    {
        $result = $this->getJson('/api/model-fields');

        $result->assertStatus(400);

        $class_type =  User::class;

        $metaFields = $this->service->getFieldsMetadata($class_type);

        $result = $this->getJson('/api/model-fields?' . http_build_query(['class_type' => User::class]));

        $this->assertEquals(count($result->getData()->data), count($metaFields));
        $this->assertEquals($result->getData()->data[0]->name, $metaFields[0]['name']);
    }

    public function testCreateOrUpdate()
    {
        $input = [
            'class_type' => User::class,
            'name' => 'description',
            'type' => MetaFieldTypeEnum::TEXT,
            'default' => 'lorem ipsum',
            'rules' => json_encode(['required', 'string', 'max:255'])
        ];
        $result = $this->actingAs($this->user, 'api')->postJson('/api/admin/model-fields', $input);

        $result->assertStatus(200);

        $this->assertEquals($result->getData()->data->class_type, $input['class_type']);
        $this->assertEquals($result->getData()->data->name, $input['name']);
        $this->assertEquals($result->getData()->data->type, $input['type']);
    }

    public function testDeleteMeta()
    {
        $result = $this->getJson('/api/model-fields?' . http_build_query(['class_type' => User::class]));

        $this->assertEquals(collect($result->getData()->data)->contains(fn ($item) => $item->name === 'description'), true);

        $input = [
            'class_type' => User::class,
            'name' => 'description',
        ];
        $result = $this->actingAs($this->user, 'api')->deleteJson('/api/admin/model-fields', $input);

        $result->assertOk();

        $result = $this->getJson('/api/model-fields?' . http_build_query(['class_type' => User::class]));

        $this->assertEquals(collect($result->getData()->data)->contains(fn ($item) => $item->name === 'description'), false);
    }

    /*
    public function testMe()
    {

        $this->mock->append(new Response(200, ['Token' => 'Token'], json_encode(["id" => 123, "name" => "name"])));
        $this->mock->append(new Response(200, ['Token' => 'Token'], json_encode([["id" => 123, "name" => "name"]])));
        $this->mock->append(new Response(200, ['Token' => 'Token'], json_encode([["id" => 123, "name" => "name"]])));

        $result = $this->actingAs($this->user, 'api')->getJson('/api/mattermost/me');

        $result->assertOk();

        $json =  [
            "server" => "localhost",
            "teams" => [
                [
                    "id" => 123,
                    "name" => "name",
                    "channels" => [[
                        "id" => 123,
                        "name" => "name",
                        "url" => "https://localhost/name/name"
                    ]]
                ]
            ]

        ];

        $result->assertJsonFragment($json);
    }

    public function testGenerateCredentials()
    {
        $this->mock->append(new Response(200, ['Token' => 'Token'], json_encode(["id" => 123, "name" => "name"])));
        $this->mock->append(new Response(200, ['Token' => 'Token'], json_encode(["status" => "ok"])));
        $this->mock->append(new Response(200, ['Token' => 'Token'], json_encode([["id" => 123, "name" => "name"]])));
        $this->mock->append(new Response(200, ['Token' => 'Token'], json_encode([["id" => 123, "name" => "name"]])));

        $result = $this->actingAs($this->user, 'api')->getJson('/api/mattermost/generate_credentials');

        $result->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'status' => ['status'],
                'user' => ['id'],
                'password'
            ],

        ]);

        $result->assertOk();
    }

    public function testResetPassword()
    {
        $this->mock->append(new Response(200, ['Token' => 'Token'], json_encode(["id" => 123, "name" => "name"])));
        $this->mock->append(new Response(200, ['Token' => 'Token'], json_encode(["status" => "ok"])));

        $result = $this->actingAs($this->user, 'api')->getJson('/api/mattermost/reset_password');

        $result->assertOk();
    }
    */
}
