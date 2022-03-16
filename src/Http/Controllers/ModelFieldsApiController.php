<?php

namespace EscolaLms\ModelFields\Http\Controllers;

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
        $classType = $request->get('class_type');
        if (empty($classType)) {
            return $this->sendError("class_type is required", 400);
        }
        $metaFields = $this->service->getFieldsMetadata($classType);
        return $this->sendResponseForResource(MetadataResource::collection($metaFields), "metaFields list retrieved successfully");
    }

    public function createOrUpdate(MetadataCreateOrUpdateRequest $request): JsonResponse
    {
        $input = $request->all();

        $field = $this->service->addOrUpdateMetadataField(
            $input['class_type'],
            $input['name'],
            $input['type'],
            $input['default'] ?? '',
            isset($input['rules']) ? json_decode($input['rules']) : null,
            1 << 0,
            isset($input['extra']) ? json_decode($input['extra']) : null,
        );

        return $this->sendResponseForResource(MetadataResource::make($field), "meta field created or updated successfully");
    }

    public function delete(MetadataDeleteRequest $request): JsonResponse
    {
        $input = $request->all();

        $bool = $this->service->removeMetaField(
            $input['class_type'],
            $input['name'],
        );

        return $bool ? $this->sendResponse(true, "meta field deleted successfully") : $this->sendError("meta field delete error", 404);
    }
}
