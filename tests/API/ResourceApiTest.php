<?php

namespace EscolaLms\ModelFields\Tests\API;

use EscolaLms\Core\Tests\CreatesUsers;
use EscolaLms\ModelFields\Tests\TestCase;
use EscolaLms\ModelFields\Enum\MetaFieldTypeEnum;
use EscolaLms\ModelFields\Enum\MetaFieldVisibilityEnum;

use EscolaLms\Core\Enums\UserRole;
use EscolaLms\ModelFields\Tests\Models\User;
use Illuminate\Support\Facades\App;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;

use EscolaLms\ModelFields\Facades\ModelFields;


class ResourceApiTest extends TestCase
{

    use CreatesUsers;

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

        ModelFields::addOrUpdateMetadataField(
            User::class,
            'title',
            'varchar',
            '',
            ['required', 'string', 'max:255']
        );

        ModelFields::addOrUpdateMetadataField(
            User::class,
            'admin_secret',
            'varchar',
            'super_secret',
            ['required', 'string', 'max:255'],
            MetaFieldVisibilityEnum::ADMIN
        );
    }

    public function testListResourceVisibility()
    {

        User::create([
            'first_name' => 'aaa',
            'last_name' => 'aaa',
            'email' => 'aaa@email.com',
            'description' => 'aaa',
            'admin_secret' => 'XXX'
        ]);

        $result = $this->getJson('/api/test-users');

        $this->assertTrue(!collect($result->getData())->contains(fn ($item) => isset($item->admin_secret) && $item->admin_secret === 'XXX'));

        $result = $this->getJson('/api/admin/test-users');

        $this->assertTrue(collect($result->getData())->contains(fn ($item) => isset($item->admin_secret) && $item->admin_secret === 'XXX'));
    }

    public function testCreateRules()
    {
        $this->assertTrue(true);
    }
}
