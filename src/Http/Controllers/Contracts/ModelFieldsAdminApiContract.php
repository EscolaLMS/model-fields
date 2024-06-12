<?php

namespace EscolaLms\ModelFields\Http\Controllers\Contracts;

use EscolaLms\ModelFields\Http\Requests\MetadataCreateOrUpdateRequest;
use EscolaLms\ModelFields\Http\Requests\MetadataDeleteRequest;
use EscolaLms\ModelFields\Http\Requests\MetadataListRequest;
use Illuminate\Http\JsonResponse;


interface ModelFieldsAdminApiContract
{
    /**
     * @OA\Get(
     *     path="/api/admin/model-fields",
     *     summary="Lists available Model extended fields",
     *     tags={"Model Fields"},
     *     @OA\Parameter(
     *         description="class type of which field is extended",
     *         in="query",
     *         name="class_type",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter (
     *         description="pagination page number",
     *         in="query",
     *         name="page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *        )
     *     ),
     *     @OA\Parameter (
     *         description="number of items per page",
     *         in="query",
     *         name="per_page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer"
     *        )
     *     ),
     *     @OA\Parameter (
     *         description="order direction",
     *         in="query",
     *         name="order",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *        )
     *     ),
     *     @OA\Parameter (
     *         description="order by field",
     *         in="query",
     *         name="order_by",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *        )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="list of available fields",
     *         @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                type="object",
     *                description="map of templates identified by a slug value",
     *                @OA\AdditionalProperties(
     *                    ref="#/components/schemas/ModelField"
     *                )
     *            )
     *         )
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="endpoint requires authentication",
     *     ),
     *     @OA\Response(
     *          response=403,
     *          description="user doesn't have required access rights",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     *
     * @param MetadataListRequest $request
     * @return JsonResponse
     */
    public function list(MetadataListRequest $request): JsonResponse;

    /**
     * @OA\Post(
     *     path="/api/admin/model-fields",
     *     summary="Create or update metafile",
     *     tags={"Admin Model Fields"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\RequestBody(
     *         description="Metadata attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Metadata")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Metadata created successfully",
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="endpoint requires authentication",
     *      ),
     *     @OA\Response(
     *          response=403,
     *          description="user doesn't have required access rights",
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="one of the parameters has invalid format",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     *
     * @param MetadataCreateOrUpdateRequest $request
     * @return JsonResponse
     */
    public function createOrUpdate(MetadataCreateOrUpdateRequest $request): JsonResponse;

    /**
     * @OA\Delete(
     *     path="/api/admin/model-fields",
     *     summary="delete metafile and related values from model",
     *     tags={"Admin Model Fields"},
     *     security={
     *         {"passport": {}},
     *     },
     *     @OA\RequestBody(
     *         description="Metadata attributes",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Metadata")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Metadata created successfully",
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="endpoint requires authentication",
     *      ),
     *     @OA\Response(
     *          response=403,
     *          description="user doesn't have required access rights",
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="one of the parameters has invalid format",
     *      ),
     *     @OA\Response(
     *          response=500,
     *          description="server-side error",
     *      ),
     * )
     *
     * @param MetadataDeleteRequest $request
     * @return JsonResponse
     */
    public function delete(MetadataDeleteRequest $request): JsonResponse;
}
