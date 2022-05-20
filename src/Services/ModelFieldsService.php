<?php

namespace EscolaLms\ModelFields\Services;

use EscolaLms\ModelFields\Models\Field;
use EscolaLms\ModelFields\Models\Metadata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;
use EscolaLms\ModelFields\Enum\MetaFieldTypeEnum;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;

class ModelFieldsService implements ModelFieldsServiceContract
{
    public function addOrUpdateMetadataField(string $class_type, string $name, string $type, string $default = '', array $rules = null, $visibility = 1 << 0, array $extra = null): Metadata
    {
        if (!MetaFieldTypeEnum::hasValue($type)) {
            throw ValidationException::withMessages([
                'type' => [sprintf('type must be one of %s', implode(",", MetaFieldTypeEnum::getValues()))],
            ]);
        }

        Cache::flush();

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

        return $bool;
    }

    public function getFieldsMetadata(string $class_type): Collection
    {
        if (config('model-fields.enabled')) {
            $key = sprintf("modelfields.%s", $class_type);

            return Cache::rememberForever($key, function () use ($class_type) {
                return Metadata::whereIn('class_type', array_merge([$class_type], class_parents($class_type)))->get();
            });
        }

        return collect([]);
    }

    public function getFieldsMetadataRules(string $class_type): array
    {
        if (config('model-fields.enabled')) {
            return $this->getFieldsMetadata($class_type)
                ->mapWithKeys(fn ($item, $key) => [$item['name'] => is_array($item['rules']) ? $item['rules'] : []])
                ->toArray();
        }

        return [];
    }

    public function castField($value, ?Metadata $field)
    {
        $type = $field['type'] ?? null;
        switch ($type) {
            case MetaFieldTypeEnum::BOOLEAN:
                return (bool) $value;
            case MetaFieldTypeEnum::NUMBER:
                return (float) ($value);
            case MetaFieldTypeEnum::JSON:
                return json_decode($value, true);
            case MetaFieldTypeEnum::VARCHAR:
            case MetaFieldTypeEnum::TEXT:
            default:
                return $value;
        }
    }

    private function checkVisibility(int $visibility = null, int $metadataFieldVisibility): bool
    {
        return  is_int($visibility) ? $visibility &  $metadataFieldVisibility : true;
    }

    public function getExtraAttributesValues(Model $model, $visibility = null): array
    {
        if (config('model-fields.enabled')) {
            $class = get_class($model);
            $key = sprintf("modelfieldsvalues.%s.%s", $class, $model->getKey());

            $fieldsCol = self::getFieldsMetadata($class);
            $fields = $fieldsCol
                ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => $item]);

            $visibilities = $fieldsCol
                ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => $item['visibility']])
                ->toArray();

            $defaults = $fieldsCol
                ->filter(fn ($value, $key) => !empty($value['default']))
                ->filter(fn ($item) =>  $this->checkVisibility($visibility, ($visibilities[$item['name']] ?? 0)))
                ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => self::castField($item['default'], $item)])
                ->toArray();

            $modelFields = Cache::rememberForever($key, function () use ($class, $model, $visibility) {
                return $model->fields()->get();
            });

            $extraAttributes = $modelFields
                ->filter(fn ($item) => $this->checkVisibility($visibility, ($visibilities[$item['name']] ?? 0)))
                ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => self::castField($item['value'], ($fields[$item['name']] ?? null))])
                ->toArray();

            return array_merge($defaults, $extraAttributes);
        }

        return [];
    }
}
