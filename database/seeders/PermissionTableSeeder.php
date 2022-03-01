<?php

namespace EscolaLms\ModelFields\Database\Seeders;

use EscolaLms\Core\Enums\UserRole;
use EscolaLms\ModelFields\Enum\MetaFieldPermissionsEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class PermissionTableSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $apiAdmin = Role::findOrCreate(UserRole::ADMIN, 'api');
        $permissions = [
            MetaFieldPermissionsEnum::METADATA_CREATE_UPDATE,
            MetaFieldPermissionsEnum::METADATA_DELETE,
            MetaFieldPermissionsEnum::METADATA_LIST,
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'api');
        }

        $apiAdmin->givePermissionTo($permissions);
    }
}
