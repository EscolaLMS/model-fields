<?php

namespace EscolaLms\ModelFields\Tests\Models;

use EscolaLms\ModelFields\Models\Field;
use EscolaLms\ModelFields\Tests\TestCase;
use EscolaLms\ModelFields\Tests\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EscolaLms\ModelFields\Models\Metadata;
use EscolaLms\ModelFields\Enum\MetaFieldTypeEnum;

class ModelsTest extends TestCase
{

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
        Metadata::create([
            'class_type' => User::class,
            'name' => 'description',
            'type' => 'text',
            'default' => 'lorem ipsum',
            'rules' => ['required', 'string', 'max:255'],
        ]);

        Metadata::create([
            'class_type' => User::class,
            'name' => 'interested_in_tests',
            'default' => true,
            'type' => 'boolean',
            'rules' => ['required', 'boolean'],
        ]);

        Metadata::create([
            'class_type' => User::class,
            'name' => 'title',
            'default' => '',
            'type' => 'varchar',
            'rules' => ['required', 'string', 'max:255'],
        ]);
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


    /*

    public function testDefaultFieldsModel()
    {

        $user = User::with(['fields'])->create(array_merge([
            'first_name' => 'aaa',
            'last_name' => 'aaa',
            'email' => 'aaa@email.com',
        ]));

        $user = User::find($user->id);

        //dd($user->toArray());

        $this->assertEquals($user->description, 'lorem ipsum');
        $this->assertEquals($user->interested_in_tests, true);

        $this->assertNull($user->title);
    }
    */
}
