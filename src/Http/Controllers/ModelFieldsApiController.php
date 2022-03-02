<?php

namespace EscolaLms\ModelFields\Http\Controllers;

use EscolaLms\Core\Http\Controllers\EscolaLmsBaseController;
use EscolaLms\Templates\Services\Contracts\TemplateVariablesServiceContract;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;
use EscolaLms\ModelFields\Http\Controllers\Contracts\ModelFieldsApiContract;
use EscolaLms\ModelFields\Http\Resources\MetadataResource;
use Illuminate\Http\Request;
use EscolaLms\ModelFields\Http\Requests\MetadataCreateOrUpdateRequest;
use EscolaLms\ModelFields\Http\Requests\MetadataDeleteRequest;

class ModelFieldsApiController extends EscolaLmsBaseController implements ModelFieldsApiContract
{
    private ModelFieldsServiceContract $service;

    public function __construct(ModelFieldsServiceContract $service)
    {
        $this->service = $service;
    }

    public function list(Request $request): JsonResponse
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
            $input['default'],
            isset($input['rules']) ? json_decode($input['rules']) : null
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

    /*

    public function create(TemplateCreateRequest $request): JsonResponse
    {
        $template = $this->templateService->insert($request->all());
        return $this->sendResponseForResource(TemplateResource::make($template), "template created successfully");
    }

    public function update(TemplateUpdateRequest $request, int $id): JsonResponse
    {
        $input = $request->all();

        $updated = $this->templateService->update($id, $input);
        if (!$updated) {
            return $this->sendError(sprintf("template id '%s' doesn't exists", $id), 404);
        }
        return $this->sendResponse($updated, "template updated successfully");
    }

    public function delete(TemplateDeleteRequest $request, int $id): JsonResponse
    {
        $deleted = $this->templateService->deleteById($id);
        if (!$deleted) {
            return $this->sendError(sprintf("template with id '%s' doesn't exists", $id), 404);
        }
        return $this->sendResponse($deleted, "template deleted successfully");
    }

    public function read(TemplateReadRequest $request, int $id): JsonResponse
    {
        $template = $this->templateService->getById($id);
        if ($template->exists) {
            return $this->sendResponseForResource(TemplateResource::make($template), "template fetched successfully");
        }
        return $this->sendError(sprintf("template with id '%s' doesn't exists", $id), 404);
    }

    public function events(TemplateReadRequest $request): JsonResponse
    {
        $vars = FacadesTemplate::getRegisteredEvents();

        return $this->sendResponse($vars, "events and handlers fetched successfully");
    }

    public function variables(TemplateReadRequest $request): JsonResponse
    {
        $vars = FacadesTemplate::getRegisteredEventsWithTokens();

        return $this->sendResponse($vars, "template vars fetched successfully");
    }

    public function preview(TemplateReadRequest $request, $id): Response
    {
        $template = Template::findOrFail($id);

        $preview = FacadesTemplate::sendPreview($request->user(), $template);

        return $this->sendResponse($preview->toArray(), "template preview fetched successfully");
    }

    public function assign(TemplateAssignRequest $request, $id): Response
    {
        $template = $request->getTemplate();

        $this->templateService->assignTemplateToModel($template, $request->input('assignable_id'));

        return $this->sendResponseForResource(TemplateResource::make($template));
    }

    public function unassign(TemplateAssignRequest $request, $id): Response
    {
        $template = $request->getTemplate();

        $this->templateService->unassignTemplateFromModel($template, $request->input('assignable_id'));

        return $this->sendResponseForResource(TemplateResource::make($template));
    }
    */
}
