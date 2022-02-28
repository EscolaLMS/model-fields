<?php

namespace EscolaLms\ModelFields\Services\Contracts;

use EscolaLms\ModelFields\Models\Metadata;
use EscolaLms\ModelFields\Models\Model;
use Illuminate\Support\Collection;

interface ModelFieldsServiceContract
{
    public function getFieldsMetadata(string $class_type): Collection;

    public function castField(mixed $value, Metadata $field): mixed;

    public function getExtraAttributesValues(Model $model): array;
}
