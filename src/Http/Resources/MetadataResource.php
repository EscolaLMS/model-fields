<?php

namespace EscolaLms\ModelFields\Http\Resources;

use EscolaLms\ModelFields\Models\Metadata;
use Illuminate\Http\Resources\Json\JsonResource;

class MetadataResource extends JsonResource
{
    /** @var Metadata $resource  */
    public $resource;

    public function __construct(Metadata $metadata)
    {
        $this->resource = $metadata;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->resource->getKey(),
            'name' => $this->resource->name,
            'type'  => $this->resource->type,
            'rules' => $this->resource->rules,
            'extra' => $this->resource->extra,
            'default' => $this->resource->default,
            'class_type' => $this->resource->class_type,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
