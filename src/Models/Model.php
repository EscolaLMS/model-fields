<?php

namespace EscolaLms\ModelFields\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use EscolaLms\ModelFields\Models\Field;
use EscolaLms\ModelFields\Models\Metadata;
use Illuminate\Support\Collection;
use EscolaLms\ModelFields\Models\Contracts\Model as ModelContract;



abstract class Model extends BaseModel implements ModelContract
{

    public static function getFieldsMetadata(): Collection
    {
        return Metadata::where('class_type', get_called_class())->get();
    }

    public static function castField($value, $field)
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
    private function getExtraAttributesValues()
    {
        $fields = self::getFieldsMetadata()
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => $item])
            ->toArray();

        $extraAttributes = $this->fields()
            ->get()
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => self::castField($item['value'], $fields[$item['name']])])
            ->toArray();

        return $extraAttributes;
    }

    public function attributesToArray()
    {
        $attributes =  parent::attributesToArray();

        $extraAttributes = $this->getExtraAttributesValues();


        return array_merge($attributes, $extraAttributes);
    }

    public function fill(array $attributes)
    {
        $fields = self::getFieldsMetadata();
        $this->extraFields = $fields->map(fn ($item) => ['name' => $item['name'], 'value' =>  $attributes[$item['name']] ?? '']);
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
