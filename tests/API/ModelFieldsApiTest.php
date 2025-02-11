<?php

namespace EscolaLms\ModelFields\Tests\API;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\ModelFields\Models\Metadata;
use EscolaLms\ModelFields\Tests\TestCase;
use EscolaLms\ModelFields\Enum\MetaFieldTypeEnum;
use EscolaLms\Core\Enums\UserRole;
use EscolaLms\ModelFields\Tests\Models\User;
use Illuminate\Support\Facades\App;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;
use Illuminate\Support\Facades\Config;

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

        Config::set('model-fields.enabled', false);
        $result = $this->getJson('/api/model-fields?' . http_build_query(['class_type' => User::class]));
        $this->assertEmpty($result->getData()->data);


        Config::set('model-fields.enabled', true);
        $result = $this->getJson('/api/model-fields?' . http_build_query(['class_type' => User::class]));
        $this->assertEquals(count($result->getData()->data), count($metaFields));
        $this->assertEquals($result->getData()->data[0]->name, $metaFields[0]['name']);
    }

    public function testListAdmin()
    {
        Config::set('model-fields.enabled', false);

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/model-fields', [
            'class_type' => User::class,
            'order_by' => 'name',
            'order' => 'ASC',
        ]);
        $this->assertEmpty($response->getData()->data);

        Config::set('model-fields.enabled', true);

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/model-fields', [
            'class_type' => User::class,
            'order_by' => 'name',
            'order' => 'ASC',
        ]);

        $this->assertEquals($response->getData()->data[0]->name, 'consents');

        $response = $this->actingAs($this->user, 'api')->json('GET', '/api/admin/model-fields', [
            'class_type' => User::class,
            'order_by' => 'name',
            'order' => 'DESC',
        ]);

        $this->assertEquals($response->getData()->data[0]->name, 'title');
    }

    public function testCreateOrUpdate()
    {
        $extraField = [
            [
                'i18n' => [
                    'pl' => 'i18n pl',
                    'en' => 'i18n en',
                ]
            ]
        ];

        $input = [
            'class_type' => User::class,
            'name' => 'description',
            'type' => MetaFieldTypeEnum::TEXT,
            'default' => 'lorem ipsum',
            'rules' => json_encode(['required', 'string', 'max:255']),
            'extra' => json_encode($extraField),
        ];

        $result = $this->actingAs($this->user, 'api')->postJson('/api/admin/model-fields', $input);

        $result->assertStatus(200);

        $this->assertEquals($result->getData()->data->class_type, $input['class_type']);
        $this->assertEquals($result->getData()->data->name, $input['name']);
        $this->assertEquals($result->getData()->data->type, $input['type']);
        $result->assertJsonFragment(['extra' => $extraField]);

        $input = [
            'class_type' => User::class,
            'name' => 'no_default_value',
            'type' => MetaFieldTypeEnum::TEXT,
        ];
        $result = $this->actingAs($this->user, 'api')->postJson('/api/admin/model-fields', $input);

        $result->assertStatus(201);
        $this->assertEquals($result->getData()->data->class_type, $input['class_type']);
        $this->assertEquals($result->getData()->data->name, $input['name']);
        $this->assertEquals($result->getData()->data->type, $input['type']);
        $this->assertEquals($result->getData()->data->default, '');
    }

    public function testDeleteMeta()
    {
        $result = $this->getJson('/api/model-fields?' . http_build_query(['class_type' => User::class]));

        $this->assertEquals(collect($result->getData()->data)->contains(fn($item) => $item->name === 'description'), true);

        $input = [
            'class_type' => User::class,
            'name' => 'description',
        ];
        $result = $this->actingAs($this->user, 'api')->deleteJson('/api/admin/model-fields', $input);

        $result->assertOk();

        $result = $this->getJson('/api/model-fields?' . http_build_query(['class_type' => User::class]));

        $this->assertEquals(collect($result->getData()->data)->contains(fn($item) => $item->name === 'description'), false);
    }
}
