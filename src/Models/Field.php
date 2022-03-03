<?php

namespace EscolaLms\ModelFields\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *      schema="ModelField",
 *      @OA\Property(
 *          property="id",
 *          description="template id",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="name",
 *          description="template name",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="value",
 *          description="",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="class_type",
 *          description="",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="class_id",
 *          description="",
 *          type="string"
 *      ),
 * )
 */
class Field extends Model
{
    protected $table = 'model_fields_values';

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'value' => 'string',
        'class_type' => 'string',
        'class_id' => 'integer',
    ];

    protected $guarded = [
        'id'
    ];
}
