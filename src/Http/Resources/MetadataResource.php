<?php

namespace EscolaLms\ModelFields\Http\Resources;

use EscolaLms\ModelFields\Models\Metadata;
use Illuminate\Http\Resources\Json\JsonResource;

class MetadataResource extends JsonResource
{
    public function __construct(Metadata $metadata)
    {
        $this->resource = $metadata;
    }

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type'  => $this->type,
            'rules' => $this->rules,
            'extra' => $this->extra,
            'default' => $this->default,
            'class_type' => $this->class_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
