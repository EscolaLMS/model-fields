<?php

namespace EscolaLms\ModelFields\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @OA\Schema(
 *      schema="Metadata",
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="name",
 *          description="name",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="value",
 *          description="value",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="default",
 *          description="default",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="rules",
 *          description="rules",
 *          type="object"
 *      ),
 *      @OA\Property(
 *          property="extra",
 *          description="extra",
 *          type="object"
 *      ),
 *      @OA\Property(
 *          property="visibility",
 *          description="visibility",
 *          type="object"
 *      ),
 *      @OA\Property(
 *          property="class_type",
 *          description="class_type",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="class_id",
 *          description="class_id",
 *          type="string"
 *      ),
 * )
 *
 * @property string $name
 * @property string $type
 * @property array $rules
 * @property array $extra
 * @property string $default
 * @property string $class_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Metadata extends Model
{
    protected $table = 'model_fields_metadata';

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'type' => 'string',
        'default' => 'string',
        'rules' => 'array',
        'extra' => 'array',
        'visibility' => 'integer',
        'class_type' => 'string',
    ];

    protected $guarded = [
        'id'
    ];
}
