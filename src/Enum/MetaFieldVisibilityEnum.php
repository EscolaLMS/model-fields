<?php

namespace EscolaLms\ModelFields\Enum;

use EscolaLms\Core\Enums\BasicEnum;

class MetaFieldVisibilityEnum extends BasicEnum
{
    const PUBLIC        = 1 << 0;
    const AUTHORIZED    = 1 << 1;
    const ADMIN         = 1 << 2;
}
