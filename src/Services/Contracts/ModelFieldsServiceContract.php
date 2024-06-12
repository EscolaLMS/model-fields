<?php

namespace EscolaLms\ModelFields\Services\Contracts;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\ModelFields\Models\Metadata;
use EscolaLms\ModelFields\Models\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ModelFieldsServiceContract
{

    public function addOrUpdateMetadataField(string $class_type, string $name, string $type, string $default = '', array $rules = null, int $visibility = 1 << 0, array $extra = null): Metadata;

    public function removeMetaField(string $class_type, string $name): bool;

    public function getFieldsMetadata(string $class_type): Collection;

    public function getFieldsMetadataListPaginated(string $class_type, ?int $perPage = 15, ?OrderDto $orderDto = null): Collection|LengthAwarePaginator;

    public function castField(mixed $value, ?Metadata $field): mixed;

    /**
     * @return array<string, string>
     */
    public function getExtraAttributesValues(Model $model, ?int $visibility = null): array;

    public function getFieldsMetadataRules(string $class_type): array;
}
