<?php

namespace EscolaLms\ModelFields\Traits;

use EscolaLms\ModelFields\Models\Field;
use Illuminate\Support\Collection;
use EscolaLms\ModelFields\Enum\MetaFieldTypeEnum;
use EscolaLms\ModelFields\Facades\ModelFields as ModelFieldsFacade;

trait ModelFields
{
    private Collection $extraFields;

    private function getExtraAttributesValues(): array
    {
        return ModelFieldsFacade::getExtraAttributesValues($this);
    }

    public function attributesToArray()
    {
        $attributes =  parent::attributesToArray();
        $extraAttributes = $this->getExtraAttributesValues();

        return array_merge($attributes, $extraAttributes);
    }

    private function convertValueForFill($value, array $field): string
    {

        $type = $field['type'];
        switch ($type) {
            case MetaFieldTypeEnum::JSON:
                return json_encode($value);
            case MetaFieldTypeEnum::BOOLEAN:
                return $value ? "true" : "";
            case MetaFieldTypeEnum::NUMBER:
            case MetaFieldTypeEnum::VARCHAR:
            case MetaFieldTypeEnum::TEXT:
            default:
                return (string) $value;
        }
    }

    public function fill(array $attributes)
    {
        $fields = ModelFieldsFacade::getFieldsMetadata(self::class)
            ->mapWithKeys(fn ($item, $key) =>  [$item['name'] => $item])
            ->toArray();

        $this->extraFields = collect($attributes)
            ->filter(fn ($item, $key) => in_array($key, array_keys($fields)))
            ->map(fn ($item, $key) => ['name' => $key, 'value' =>  self::convertValueForFill($item, $fields[$key])]);

        return parent::fill($attributes);
    }

    public function save(array $options = [])
    {
        $extraFields = $this->extraFields;
        unset($this->extraFields);
        $savedState = parent::save($options);
        $values = $extraFields->toArray();
        $names = $extraFields->map(fn ($item) => $item['name'])->toArray();
        $this->fields()->whereIn('name', $names)->delete();
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

    public function setAttribute($key, $value)
    {

        $metaFields =  ModelFieldsFacade::getFieldsMetadata(static::class)
            ->mapWithKeys(fn ($item) =>  [$item['name'] => $item])
            ->toArray();

        if (array_key_exists($key, $metaFields)) {

            $this->extraFields = isset($this->extraFields) ? $this->extraFields->prepend(['name' => $key, 'value' => $value]) : collect([
                ['name' => $key, 'value' => $value]
            ]);
            return;
        }

        return parent::setAttribute($key, $value);
    }

    public function delete()
    {
        $this->fields()->delete();
        parent::delete();
    }

    public function fields()
    {
        return $this->morphMany(Field::class, 'class');
    }
}
