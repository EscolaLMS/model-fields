<?php

namespace EscolaLms\ModelFields\Http\Controllers;

use EscolaLms\Core\Dtos\OrderDto;
use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\ModelFields\Http\Controllers\Contracts\ModelFieldsAdminApiContract;
use EscolaLms\ModelFields\Http\Requests\MetadataCreateOrUpdateRequest;
use EscolaLms\ModelFields\Http\Requests\MetadataDeleteRequest;
use EscolaLms\ModelFields\Http\Requests\MetadataListRequest;
use EscolaLms\ModelFields\Http\Resources\MetadataResource;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;
use Illuminate\Http\JsonResponse;

class ModelFieldsAdminApiController extends EscolaLmsBaseController implements ModelFieldsAdminApiContract
{
    private ModelFieldsServiceContract $service;

    public function __construct(ModelFieldsServiceContract $service)
    {
        $this->service = $service;
    }

    public function list(MetadataListRequest $request): JsonResponse
    {
        /** @var string $classType */
        $classType = $request->get('class_type');
        if (empty($classType)) {
            return $this->sendError("class_type is required", 400);
        }
        /** @var int $perPage */
        $perPage = $request->get('per_page', 15);
        $metaFields = $this->service->getFieldsMetadataListPaginated($classType, $perPage, OrderDto::instantiateFromRequest($request));
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
