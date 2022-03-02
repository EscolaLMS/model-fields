<?php

namespace EscolaLms\ModelFields\Facades;

use Illuminate\Support\Facades\Facade;
use EscolaLms\ModelFields\Services\Contracts\ModelFieldsServiceContract;

class ModelFields extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ModelFieldsServiceContract::class;
    }
}
