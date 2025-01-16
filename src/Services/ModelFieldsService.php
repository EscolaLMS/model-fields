<?php

namespace EscolaLms\ModelFields\Services;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\ModelFields\Enum\MetaFieldTypeEnum;
use EscolaLms\ModelFields\Models\Field;
use EscolaLms\ModelFields\Models\Metadata;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ModelFieldsService implements ModelFieldsServiceContract
{

    public static $cache = ['metadata' => [], 'fields' => []];

    public function addOrUpdateMetadataField(string $class_type, string $name, string $type, string $default = '', array $rules = null, int $visibility = 1 << 0, array $extra = null): Metadata
    {
        if (!MetaFieldTypeEnum::hasValue($type)) {
            throw ValidationException::withMessages([
                'type' => [sprintf('type must be one of %s', implode(",", MetaFieldTypeEnum::getValues()))],
            ]);
        }

        Cache::flush();
        self::$cache = ['metadata' => [], 'fields' => []];

        return Metadata::updateOrCreate(
            ['class_type' => $class_type, 'name' => $name],
            ['type' => $type, 'default' => $default, 'rules' => $rules, 'visibility' => $visibility, 'extra' => $extra]
        );
    }

    public function removeMetaField(string $class_type, string $name): bool
    {
        $bool = Metadata::where(
            ['class_type' => $class_type, 'name' => $name]
        )->delete();

        Field::where(
            ['class_type' => $class_type, 'name' => $name]
        )->delete();

        Cache::flush();
        self::$cache = ['metadata' => [], 'fields' => []];

        return $bool;
    }

    public function getFieldsMetadata(string $class_type): Collection
    {
        if (isset(self::$cache['metadata'][$class_type])) {
            return self::$cache['metadata'][$class_type];
        }

        if (config('model-fields.enabled') && /*$tableExist &&*/ class_exists(Cache::class, false)) {
            $key = sprintf("modelfields.%s", $class_type);

            $cachedValue = Cache::get($key);

            if ($cachedValue) {
                self::$cache['metadata'][$class_type] = $cachedValue;
                return $cachedValue;
            }

            $classTypes = class_parents($class_type) ? array_merge([$class_type], class_parents($class_type)) : [$class_type];
            $values = Metadata::whereIn('class_type', $classTypes)->get();
            Cache::put($key, $values);
            self::$cache['metadata'][$class_type] = $values;
            return $values;
        }

        return collect([]);
    }

    public function getFieldsMetadataListPaginated(string $class_type, ?int $perPage = 15, ?OrderDto $orderDto = null): Collection|LengthAwarePaginator
    {
        if (!config('model-fields.enabled')) {
            return collect([]);
        }

        $classTypes = class_parents($class_type) ? array_merge([$class_type], class_parents($class_type)) : [$class_type];
        $query = Metadata::query()
            ->whereIn('class_type', $classTypes)
            ->orderBy($orderDto?->getOrderBy() ?? 'id', $orderDto?->getOrder() ?? 'asc');

        return $query->paginate($perPage);
    }

    public function getFieldsMetadataRules(string $class_type): array
    {
        if (config('model-fields.enabled')) {
            return $this->getFieldsMetadata($class_type)
                ->mapWithKeys(fn($item, $key) => [$item['name'] => is_array($item['rules']) ? $item['rules'] : []])
                ->toArray();
        }

        return [];
    }

    public function castField(mixed $value, ?Metadata $field): mixed
    {
        $type = $field['type'] ?? null;
        switch ($type) {
            case MetaFieldTypeEnum::BOOLEAN:
                return (bool)$value;
            case MetaFieldTypeEnum::NUMBER:
                return (float)($value);
            case MetaFieldTypeEnum::JSON:
                return json_decode($value, true);
            case MetaFieldTypeEnum::VARCHAR:
            case MetaFieldTypeEnum::TEXT:
            default:
                return $value;
        }
    }

    private function checkVisibility(?int $visibility = null, int $metadataFieldVisibility): int|bool
    {
        return is_int($visibility) ? $visibility & $metadataFieldVisibility : true;
    }

    /**
     * @return array<string, string>
     */
    public function getExtraAttributesValues(Model $model, ?int $visibility = null): array
    {

        if (config('model-fields.enabled')) {
            if (!array_key_exists('id', $model->getAttributes())) {
                return [];
            }

            $class = get_class($model);
            $key = sprintf("modelfieldsvalues.%s.%s", $class, $model->getKey());

            if (isset(self::$cache['values'][$key])) {
                return self::$cache['values'][$key];
            }

            $fieldsCol = self::getFieldsMetadata($class);
            $fields = $fieldsCol
                ->mapWithKeys(fn($item, $key) => [$item['name'] => $item]);

            $visibilities = $fieldsCol
                ->mapWithKeys(fn($item, $key) => [$item['name'] => $item['visibility']])
                ->toArray();

            $defaults = $fieldsCol
                ->filter(fn($value, $key) => !empty($value['default']))
                ->filter(fn($item) => $this->checkVisibility($visibility, ($visibilities[$item['name']] ?? 0)))
                ->mapWithKeys(fn($item, $key) => [$item['name'] => self::castField($item['default'], $item)])
                ->toArray();


            $modelFields = Cache::rememberForever($key, function () use ($model) {
                // @phpstan-ignore-next-line
                return $model->fields()->get();
            });

            $extraAttributes = $modelFields
                ->filter(fn($item) => $this->checkVisibility($visibility, ($visibilities[$item['name']] ?? 0)))
                ->mapWithKeys(fn($item, $key) => [$item['name'] => self::castField($item['value'], ($fields[$item['name']] ?? null))])
                ->toArray();

            $values = array_merge($defaults, $extraAttributes);

            self::$cache['values'][$key] = $values;

            return $values;
        }

        return [];
    }
}
