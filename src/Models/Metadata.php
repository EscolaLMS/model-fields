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
class Metadata extends Model
{
    protected $table = 'model_fields_metadata';

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'type' => 'string',
        'rules' => 'array',
        'class_type' => 'string',
    ];

    protected $guarded = [
        'id'
    ];
}
