<?php

namespace EscolaLms\ModelFields\Http\Controllers\Contracts;

use EscolaLms\ModelFields\Http\Requests\MetadataListRequest;
use Illuminate\Http\JsonResponse;


interface ModelFieldsApiContract
{
    /**
     * @OA\Get(
     *     path="/api/model-fields",
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
}
