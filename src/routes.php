<?php

use Illuminate\Support\Facades\Route;
use EscolaLms\ModelFields\Http\Controllers\ModelFieldsApiController;

Route::group(['prefix' => 'api/model-fields'], function () {
    Route::get('/', [ModelFieldsApiController::class, 'list']);
    // Route::get('/reset_password', [MattermostController::class, 'resetPassword']);
    // Route::get('/generate_credentials', [MattermostController::class, 'generateCredentials']);
});

Route::group(['prefix' => 'api/admin/model-fields', 'middleware' => ['auth:api']], function () {
    Route::post('/', [ModelFieldsApiController::class, 'createOrUpdate']);
    Route::delete('/', [ModelFieldsApiController::class, 'delete']);
});
