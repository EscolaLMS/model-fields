<?php

namespace EscolaLms\ModelFields\Enum;

use EscolaLms\Core\Enums\BasicEnum;

class MetaFieldTypeEnum extends BasicEnum
{
    const BOOLEAN   = 'boolean';
    const NUMBER   = 'number';
    const VARCHAR   = 'varchar';
    const TEXT  = 'text';
    const JSON  = 'json';
}
