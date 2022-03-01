<?php

namespace EscolaLms\ModelFields\Services;

use Illuminate\Database\Eloquent\Model as BaseModel;
use EscolaLms\ModelFields\Models\Field;
use EscolaLms\ModelFields\Models\Metadata;
use EscolaLms\ModelFields\Models\Model;
use Illuminate\Support\Collection;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;



class ModelFieldsService implements ModelFieldsServiceContract
{
    // todo this to service
    public function getFieldsMetadata(string $class_type): Collection
    {
        return Metadata::where('class_type', $class_type)->get();
    }

    // todo this to service
    public function castField(mixed $value, Metadata $field): mixed
    {
        $type = $field['type'];
        switch ($type) {
            case "boolean":
                return (bool) intval($value);
            case "number":
                return (float) ($value);
            default:
                return $value;
        }
    }


    // TODO this should cached somehow
    public function getExtraAttributesValues(Model $model): array
    {

        $fieldsCol =  self::getFieldsMetadata(get_class($model));

        $fields = $fieldsCol
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => $item]);


        $defaults = $fieldsCol
            ->filter(fn ($value, $key) => !empty($value['default']))
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => self::castField($item['default'], $item)])
            ->toArray();

        $extraAttributes = $model->fields()
            ->get()
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => self::castField($item['value'], $fields[$item['name']])])
            ->toArray();

        return array_merge($defaults, $extraAttributes);
    }
}
