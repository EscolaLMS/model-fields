<?php

namespace EscolaLms\ModelFields\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use EscolaLms\ModelFields\Models\Field;
use EscolaLms\ModelFields\Models\Metadata;
use Illuminate\Support\Collection;

use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;
use EscolaLms\ModelFields\Services\ModelFieldsService;
use Illuminate\Support\Facades\App;

abstract class Model extends BaseModel
{
    private ModelFieldsServiceContract $service;
    private Collection $extraFields;

    public function __construct(array $attributes = [])
    {
        $this->service = App::make(ModelFieldsServiceContract::class);
        parent::__construct($attributes);
    }
    // todo this to service
    /*
    public static function getFieldsMetadata(): Collection
    {
        return $this->service->getFieldsMetadata(get_called_class());
        //return Metadata::where('class_type', get_called_class())->get();
    }
    */

    // todo this to service
    public static function castField($value, $field)
    {

        // move thgiu
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
    private function getExtraAttributesValues(): array
    {
        return $this->service->getExtraAttributesValues($this);

        /*

        $fieldsCol =  self::getFieldsMetadata();


        $fields = $fieldsCol
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => $item])
            ->toArray();

        $defaults = $fieldsCol
            ->filter(fn ($value, $key) => !empty($value['default']))
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => self::castField($item['default'], $item)])
            ->toArray();

        $extraAttributes = $this->fields()
            ->get()
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => self::castField($item['value'], $fields[$item['name']])])
            ->toArray();

        return array_merge($defaults, $extraAttributes);
        */
    }


    public function attributesToArray()
    {
        $attributes =  parent::attributesToArray();
        $extraAttributes = $this->getExtraAttributesValues();

        return array_merge($attributes, $extraAttributes);
    }


    public function fill(array $attributes)
    {

        $field_names = $this->service
            ->getFieldsMetadata(static::class)
            ->map(fn ($item) => $item['name'])
            ->toArray();

        $this->extraFields = collect($attributes)
            ->filter(fn ($item, $key) => in_array($key, $field_names))
            ->map(fn ($item, $key) => ['name' => $key, 'value' =>  $item]);

        return parent::fill($attributes);
    }

    public function save(array $options = [])
    {
        $extraFields = $this->extraFields;
        unset($this->extraFields);
        $savedState = parent::save($options);
        $values = $extraFields->toArray();
        // FIXME: This is stupid, there should a way to handle updateOrCreate on polymorphic one-to-many
        $this->fields()->delete();
        $this->fields()->createMany($values);
        return $savedState;
    }

    public function getAttribute($key)
    {
        $attribute = parent::getAttribute($key);

        if (is_null($attribute)) {
            $fields = $this->getExtraAttributesValues();

            if (array_key_exists($key, $fields)) {
                return $fields[$key];
            }
        }

        return $attribute;
    }

    public function fields()
    {
        return $this->morphMany(Field::class, 'class');
    }
}
