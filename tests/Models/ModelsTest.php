<?php

namespace EscolaLms\ModelFields\Tests\Models;

use EscolaLms\ModelFields\Models\Field;
use EscolaLms\ModelFields\Tests\TestCase;
use EscolaLms\ModelFields\Tests\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EscolaLms\ModelFields\Models\Metadata;
use EscolaLms\ModelFields\Enum\MetaFieldTypeEnum;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;
use EscolaLms\ModelFields\Services\ModelFieldsService;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

class ModelsTest extends TestCase
{

    use DatabaseTransactions;
    private ModelFieldsServiceContract $service;

    public function setUp(): void
    {
        parent::setUp();

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
    }

    public function testInvalidType()
    {

        $this->expectException(ValidationException::class);

        $this->service->addOrUpdateMetadataField(
            User::class,
            'title',
            'invalid_type',
            '',
            ['required', 'string', 'max:255']
        );
    }

    public function testDeleteFields()
    {
        $extraAttributes = [
            'description' => 'aaa',
            'interested_in_tests' => false,
            'aaaa' => 'aaaa'
        ];

        $user = User::create(array_merge([
            'first_name' => 'aaa',
            'last_name' => 'aaa',
            'email' => 'aaa@email.com',
        ], $extraAttributes));

        $user = User::find($user->id);

        $this->assertEquals(Field::all()->count(), 2);

        $user->delete();

        $this->assertEquals(Field::all()->count(), 0);
    }


    public function testModel()
    {
        $extraAttributes = [
            'description' => 'aaa',
            'interested_in_tests' => false,
            'aaaa' => 'aaaa'
        ];


        $user = User::create(array_merge([
            'first_name' => 'aaa',
            'last_name' => 'aaa',
            'email' => 'aaa@email.com',
        ], $extraAttributes));

        $user->fill(['a' => 'nb']);

        $user = User::find($user->id);


        $this->assertEquals($user->description, $extraAttributes['description']);
        $this->assertEquals($user->interested_in_tests, $extraAttributes['interested_in_tests']);


        $this->assertNull($user->aaaa);
    }



    public function testDefaultFieldsModel()
    {

        $user = User::with(['fields'])->create(array_merge([
            'first_name' => 'aaa',
            'last_name' => 'aaa',
            'email' => 'aaa@email.com',
        ]));

        $user = User::find($user->id);

        $this->assertEquals($user->description, 'lorem ipsum');
        $this->assertEquals($user->interested_in_tests, true);

        $this->assertNull($user->title);
    }
}
