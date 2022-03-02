<?php

namespace EscolaLms\ModelFields\Http\Requests;

use EscolaLms\ModelFields\Models\Metadata;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class MetadataDeleteRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('delete', Metadata::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string'],
            'class_type' => ['required', 'string'],
        ];
    }
}
