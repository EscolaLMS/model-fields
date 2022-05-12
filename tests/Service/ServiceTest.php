<?php

namespace EscolaLms\ModelFields\Tests\Service;

use EscolaLms\ModelFields\Models\Field;
use EscolaLms\ModelFields\Tests\TestCase;
use EscolaLms\ModelFields\Tests\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use EscolaLms\ModelFields\Enum\MetaFieldTypeEnum;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Faker\Generator as Faker;

class ServiceTest extends TestCase
{

    use DatabaseTransactions;
    private ModelFieldsServiceContract $service;

    public function setUp(): void
    {
        parent::setUp();

        $this->service = App::make(ModelFieldsServiceContract::class);
        $this->faker = App::make(Faker::class);

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

        $this->service->addOrUpdateMetadataField(
            User::class,
            'extra_points',
            MetaFieldTypeEnum::NUMBER,
            123,
            ['required', 'integer']
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
            'aaaa' => 'aaaa',
        ];

        $user = User::create(array_merge([
            'first_name' => 'aaa',
            'last_name' => 'aaa',
            'email' => $this->faker->unique()->safeEmail,
        ], $extraAttributes));

        $user = User::find($user->id);

        $this->assertEquals(Field::all()->count(), 2);

        $user->forceDelete();

        $this->assertEquals(Field::all()->count(), 0);
    }

    public function testDeleteMetaFields()
    {

        $this->service->addOrUpdateMetadataField(
            User::class,
            'extra_description',
            MetaFieldTypeEnum::TEXT,
            'lorem ipsum',
            ['required', 'string', 'max:255']
        );

        $extraAttributes = [
            'extra_description' => 'xyz',

        ];

        $user = User::create(array_merge([
            'first_name' => 'aaa',
            'last_name' => 'aaa',
            'email' => $this->faker->unique()->safeEmail,
        ], $extraAttributes));

        $user = User::find($user->id);

        $this->assertEquals($user->extra_description, 'xyz');

        $this->assertEquals(Field::all()->count(), 1);

        $this->service->removeMetaField(
            User::class,
            'extra_description'
        );

        $this->assertNull($user->extra_description);

        $this->assertEquals(Field::all()->count(), 0);
    }

    public function testModel()
    {
        $extraAttributes = [
            'description' => 'aaa',
            'interested_in_tests' => false,
            'aaaa' => 'aaaa',
            'consents' => ['consent1' => true, 'consent2' => false],
            'extra_points' => 1000
        ];

        $user = User::create(array_merge([
            'first_name' => 'aaa',
            'last_name' => 'aaa',
            'email' => $this->faker->unique()->safeEmail,
        ], $extraAttributes));

        $user->fill(['a' => 'nb']);

        $user = User::find($user->id);

        $this->assertEquals($user->description, $extraAttributes['description']);
        $this->assertEquals($user->interested_in_tests, $extraAttributes['interested_in_tests']);

        $this->assertEquals($user->consents, $extraAttributes['consents']);
        $this->assertEquals($user->extra_points, $extraAttributes['extra_points']);

        $this->assertNull($user->aaaa);

        $user->description = 'abc';
        $user->interested_in_tests = true;
        $user->save();
        $user = User::find($user->id);
        $this->assertEquals($user->description, 'abc');
        $this->assertEquals($user->interested_in_tests, true);

        $user->update([
            'description' => 'zzz',
            'interested_in_tests' => false
        ]);

        $user = User::find($user->id);

        $this->assertEquals($user->description, 'zzz');
        $this->assertEquals($user->interested_in_tests, false);

        // update only one attribute, rest should remain untouched
        $user->update([
            'interested_in_tests' => true
        ]);

        $this->assertEquals($user->description, 'zzz');
        $this->assertEquals($user->interested_in_tests, true);
    }

    public function testDefaultFieldsModel()
    {

        $user = User::with(['fields'])->create(array_merge([
            'first_name' => 'aaa',
            'last_name' => 'aaa',
            'email' => $this->faker->unique()->safeEmail,
        ]));

        $user = User::find($user->id);

        $this->assertEquals($user->description, 'lorem ipsum');
        $this->assertEquals($user->interested_in_tests, true);

        $this->assertNull($user->title);
    }

    public function testFirstOrCreate()
    {
        $user = User::create([
            'email' => $this->faker->unique()->safeEmail,
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'description' => 'aaa',
        ]);

        $existingUser = User::firstOrCreate(
            ['email' => $user->email, 'description' => $user->description],
        );

        $this->assertEquals($existingUser->getKey(), $user->getKey());
        $this->assertEquals($existingUser->first_name, $user->first_name);
        $this->assertEquals($existingUser->description, $user->description);

        $email = $this->faker->unique()->safeEmail;
        $newUser = User::firstOrCreate(
            ['first_name' => 'first_name', 'description' => 'bbb'],
            ['email' => $email, 'last_name' => 'last_name']
        );

        $this->assertNotEquals($newUser->getKey(), $user->getKey());
        $this->assertEquals($newUser->first_name, 'first_name');
        $this->assertEquals($newUser->description, 'bbb');
        $this->assertEquals($newUser->email, $email);
    }

    public function testGetFieldsMetadata(): void
    {
        $this->setPackageStatus(false);
        $rules = $this->service->getFieldsMetadata(User::class);
        $this->assertEmpty($rules);

        $this->setPackageStatus(true);
        $rules = $this->service->getFieldsMetadata(User::class);
        $this->assertNotEmpty($rules);
    }

    public function testGetFieldsMetadataRules(): void
    {
        $this->setPackageStatus(false);
        $rules = $this->service->getFieldsMetadataRules(User::class);
        $this->assertEmpty($rules);

        $this->setPackageStatus(true);
        $rules = $this->service->getFieldsMetadataRules(User::class);
        $this->assertNotEmpty($rules);
    }

    public function testGetExtraAttributesValues(): void
    {
        $extraAttributes = [
            'description' => 'aaa',
            'interested_in_tests' => false,
            'aaaa' => 'aaaa',
            'consents' => ['consent1' => true, 'consent2' => false],
            'extra_points' => 1000
        ];

        $user = User::create(array_merge([
            'first_name' => 'aaa',
            'last_name' => 'aaa',
            'email' => $this->faker->unique()->safeEmail,
            'description' => 'aaa',
            'interested_in_tests' => false,
        ], $extraAttributes));

        $this->setPackageStatus(false);
        $rules = $this->service->getExtraAttributesValues($user);
        $this->assertEmpty($rules);

        $this->setPackageStatus(true);
        $rules = $this->service->getExtraAttributesValues($user);
        $this->assertNotEmpty($rules);
    }

    private function setPackageStatus(bool $enable)
    {
        Config::set('model-fields.enabled', $enable);
    }
}
