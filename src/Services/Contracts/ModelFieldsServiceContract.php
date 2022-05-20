<?php

namespace EscolaLms\ModelFields\Services\Contracts;

use EscolaLms\ModelFields\Models\Metadata;
use EscolaLms\ModelFields\Models\Model;
use Illuminate\Support\Collection;

interface ModelFieldsServiceContract
{

    public function addOrUpdateMetadataField(string $class_type, string $name, string $type, string $default = '', array $rules = null, $visibility = 1 << 0, array $extra = null): Metadata;

    public function removeMetaField(string $class_type, string $name);

    public function getFieldsMetadata(string $class_type): Collection;

    public function castField($value, ?Metadata $field);

    public function getExtraAttributesValues(Model $model, $visibility = null): array;

    public function getFieldsMetadataRules(string $class_type): array;
}
