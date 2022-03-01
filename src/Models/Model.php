<?php

namespace EscolaLms\ModelFields\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;
use EscolaLms\ModelFields\Models\Field;
use EscolaLms\ModelFields\Models\Metadata;
use Illuminate\Support\Collection;

use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;
use EscolaLms\ModelFields\Services\ModelFieldsService;
use Illuminate\Support\Facades\App;
use EscolaLms\ModelFields\Enum\MetaFieldTypeEnum;

abstract class Model extends BaseModel
{
    private ModelFieldsServiceContract $service;
    private Collection $extraFields;

    public function __construct(array $attributes = [])
    {
        $this->service = App::make(ModelFieldsServiceContract::class);
        parent::__construct($attributes);
    }

    private function getExtraAttributesValues(): array
    {
        return $this->service->getExtraAttributesValues($this);
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
            case MetaFieldTypeEnum::NUMBER:
            case MetaFieldTypeEnum::VARCHAR:
            case MetaFieldTypeEnum::TEXT:
            default:
                return (string) $value;
        }
    }


    public function fill(array $attributes)
    {
        $fields = $this->service
            ->getFieldsMetadata(static::class)
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

    public function setAttribute($key, $value)
    {

        $metaFields = $this->service
            ->getFieldsMetadata(static::class)
            ->mapWithKeys(fn ($item) =>  [$item['name'] => $item])
            ->toArray();

        if (array_key_exists($key, $metaFields)) {

            $this->extraFields = isset($this->extraFields) ? $this->extraFields->prepend(['name' => 'description', 'value' => $value]) : collect([
                ['name' => 'description', 'value' => $value]
            ]);
            return;
        }

        return parent::setAttribute($key, $value);


        if (array_key_exists($key, $fields)) {
            return $fields[$key];
        } else {
            return parent::setAttribute($key, $value);
        }
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
