<?php

namespace EscolaLms\ModelFields\Tests\Http\Requests;

use EscolaLms\ModelFields\Tests\Models\User;

use Illuminate\Foundation\Http\FormRequest;
use EscolaLms\ModelFields\Facades\ModelFields;

class UserCreateRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge([
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['required', 'unique:users'],
        ], ModelFields::getFieldsMetadataRules(User::class));
    }
}
