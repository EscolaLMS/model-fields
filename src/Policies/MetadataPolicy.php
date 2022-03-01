<?php

namespace EscolaLms\ModelFields\Policies;

use EscolaLms\Core\Models\User;
use EscolaLms\ModelFields\Enum\MetaFieldPermissionsEnum;
use EscolaLms\ModelFields\Models\Metadata;
use Illuminate\Auth\Access\HandlesAuthorization;

class MetadataPolicy
{
    use HandlesAuthorization;

    public function list(?User $user): bool
    {
        return true;
    }

    public function createOrUpdate(?User $user): bool
    {
        return !is_null($user) && $user->can(MetaFieldPermissionsEnum::METADATA_CREATE_UPDATE);
    }

    public function delete(?User $user, Metadata $template = null): bool
    {
        return !is_null($user) && $user->can(MetaFieldPermissionsEnum::METADATA_DELETE);
    }
}
