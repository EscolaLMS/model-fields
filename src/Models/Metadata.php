<?php

namespace EscolaLms\ModelFields\Models;

use EscolaLms\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use EscolaLms\Templates\Database\Factories\TemplateFactory;
use EscolaLms\Templates\Services\Contracts\TemplateServiceContract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        'class_type' => 'string',
    ];

    protected $guarded = [
        'id'
    ];
}
