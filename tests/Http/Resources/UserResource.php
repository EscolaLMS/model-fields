<?php

namespace EscolaLms\ModelFields\Tests\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use EscolaLms\ModelFields\Tests\Models\User;
use EscolaLms\ModelFields\Facades\ModelFields;
use EscolaLms\ModelFields\Enum\MetaFieldVisibilityEnum;

class UserResource extends JsonResource
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function toArray($request)
    {
        return array_merge([
            'first_name' => $this->user->first_name,
            'last_name'  => $this->user->last_name,
            'email' => $this->user->email,
        ], ModelFields::getExtraAttributesValues($this->user, MetaFieldVisibilityEnum::PUBLIC));
    }
}
