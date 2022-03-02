<?php

namespace EscolaLms\ModelFields\Enum;

use EscolaLms\Core\Enums\BasicEnum;

class MetaFieldPermissionsEnum extends BasicEnum
{
    const METADATA_CREATE_UPDATE = 'metadata_create_update';
    const METADATA_DELETE = 'metadata_delete';
    const METADATA_LIST = 'metadata_list';
}
