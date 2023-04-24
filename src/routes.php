<?php

use EscolaLms\ModelFields\Http\Controllers\ModelFieldsAdminApiController;
use Illuminate\Support\Facades\Route;
use EscolaLms\ModelFields\Http\Controllers\ModelFieldsApiController;

Route::group(['prefix' => 'api/model-fields'], function () {
    Route::get('/', [ModelFieldsApiController::class, 'list']);
});

Route::group(['prefix' => 'api/admin/model-fields', 'middleware' => ['auth:api']], function () {
    Route::get('/', [ModelFieldsAdminApiController::class, 'list']);
    Route::post('/', [ModelFieldsAdminApiController::class, 'createOrUpdate']);
    Route::delete('/', [ModelFieldsAdminApiController::class, 'delete']);
});
