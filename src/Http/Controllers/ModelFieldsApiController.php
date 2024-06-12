<?php

namespace EscolaLms\ModelFields\Http\Controllers;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use Illuminate\Http\JsonResponse;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;
use EscolaLms\ModelFields\Http\Controllers\Contracts\ModelFieldsApiContract;
use EscolaLms\ModelFields\Http\Resources\MetadataResource;
use EscolaLms\ModelFields\Http\Requests\MetadataCreateOrUpdateRequest;
use EscolaLms\ModelFields\Http\Requests\MetadataDeleteRequest;
use EscolaLms\ModelFields\Http\Requests\MetadataListRequest;

class ModelFieldsApiController extends EscolaLmsBaseController implements ModelFieldsApiContract
{
    private ModelFieldsServiceContract $service;

    public function __construct(ModelFieldsServiceContract $service)
    {
        $this->service = $service;
    }

    public function list(MetadataListRequest $request): JsonResponse
    {
        /** @var string|false $classType */
        $classType = $request->get('class_type');
        if (empty($classType)) {
            return $this->sendError("class_type is required", 400);
        }
        $metaFields = $this->service->getFieldsMetadata($classType);
        return $this->sendResponseForResource(MetadataResource::collection($metaFields), "metaFields list retrieved successfully");
    }
}
