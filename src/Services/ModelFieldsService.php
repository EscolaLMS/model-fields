<?php

namespace EscolaLms\ModelFields\Services;

use EscolaLms\ModelFields\Models\Field;
use EscolaLms\ModelFields\Models\Metadata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;
use EscolaLms\ModelFields\Enum\MetaFieldTypeEnum;
use Illuminate\Validation\ValidationException;
// use Illuminate\Support\Facades\Cache;


class ModelFieldsService implements ModelFieldsServiceContract
{


    public function addOrUpdateMetadataField(string $class_type, string $name, string $type, string $default = '', array $rules = null, $visibility = 1 << 0): Metadata
    {
        if (!MetaFieldTypeEnum::hasValue($type)) {
            throw ValidationException::withMessages([
                'type' => [sprintf('type must be one of %s', implode(",", MetaFieldTypeEnum::getValues()))],
            ]);
        }
        return Metadata::updateOrCreate(
            ['class_type' => $class_type, 'name' => $name],
            ['type' => $type, 'default' => $default, 'rules' => $rules, 'visibility' => $visibility]
        );

        // Cache::tags([sprintf("modelfields.%s", $class_type)])->flush();
    }

    public function removeMetaField(string $class_type, string $name): bool
    {
        $bool = Metadata::where(
            ['class_type' => $class_type, 'name' => $name]
        )->delete();

        Field::where(
            ['class_type' => $class_type, 'name' => $name]
        )->delete();

        // Cache::tags([sprintf("modelfields.%s", $class_type)])->flush();

        return $bool;
    }

    public function getFieldsMetadata(string $class_type): Collection
    {
        $key = sprintf("modelfields.meta.%s", $class_type);
        $tag = sprintf("modelfields.%s", $class_type);
        // if (!Cache::has($key)) {
            $fields = Metadata::whereIn('class_type', array_merge([$class_type], class_parents($class_type)))->get();
            // Cache::tags([$tag])->put($key, $fields);
        }
        // return Cache::tags([$tag])->get($key);
        return $fields;
    }

    public function getFieldsMetadataRules(string $class_type): array
    {
        return $this->getFieldsMetadata($class_type)
            ->mapWithKeys(fn ($item, $key) => [$item['name'] => $item['rules']])
            ->toArray();
    }

    public function castField($value, Metadata $field)
    {
        $type = $field['type'];
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
        $fieldsCol = self::getFieldsMetadata(get_class($model));



        $fields = $fieldsCol
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => $item]);

        $visibilities = $fieldsCol
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => $item['visibility']])
            ->toArray();

        $defaults = $fieldsCol
            ->filter(fn ($value, $key) => !empty($value['default']))
            //->filter(fn ($item) => is_int($visibility) ? $visibility >= $visibilities[$item['name']] : true)
            ->filter(fn ($item) => $this->checkVisibility($visibility, $visibilities[$item['name']]))
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => self::castField($item['default'], $item)])
            ->toArray();

        $extraAttributes = $model->fields()
            ->get()
            //            ->filter(fn ($item) => is_int($visibility) ? $visibility >= $visibilities[$item['name']] : true)
            ->filter(fn ($item) => $this->checkVisibility($visibility, $visibilities[$item['name']]))
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => self::castField($item['value'], $fields[$item['name']])])
            ->toArray();

        return array_merge($defaults, $extraAttributes);
    }
}
