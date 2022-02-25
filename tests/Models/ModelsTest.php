<?php

namespace EscolaLms\ModelFields\Tests\Models;

use EscolaLms\ModelFields\Models\Field;
use EscolaLms\ModelFields\Tests\TestCase;
use EscolaLms\ModelFields\Tests\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EscolaLms\ModelFields\Models\Metadata;

class ModelsTest extends TestCase
{

    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testModel()
    {

        Metadata::create([
            'class_type' => User::class,
            'name' => 'description',
            'type' => 'text',
            'rules' => ['required', 'string', 'max:255'],
        ]);

        Metadata::create([
            'class_type' => User::class,
            'name' => 'interested_in_tests',
            'type' => 'boolean',
            'rules' => ['required', 'boolean'],
        ]);

        $extraAttributes = [
            'description' => 'aaa',
            'interested_in_tests' => false,
            'aaaa' => 'aaaa'
        ];

        $user = User::with(['fields'])->create(array_merge([
            'first_name' => 'aaa',
            'last_name' => 'aaa',
            'email' => 'aaa@email.com',
        ], $extraAttributes));

        $user = User::find($user->id);


        $this->assertEquals($user->description, $extraAttributes['description']);
        $this->assertEquals($user->interested_in_tests, $extraAttributes['interested_in_tests']);

        $this->assertNull($user->aaaa);
    }
}
