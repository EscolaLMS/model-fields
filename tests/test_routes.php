<?php

use Illuminate\Support\Facades\Route;
use EscolaLms\ModelFields\Http\Controllers\ModelFieldsApiController;
use EscolaLms\ModelFields\Tests\Http\Controllers\TestsModelFieldsApiController;

Route::group(['prefix' => 'api/test-users'], function () {
    Route::get('/', [TestsModelFieldsApiController::class, 'list']);
});

Route::group(['prefix' => 'api/admin/test-users'], function () {
    Route::get('/', [TestsModelFieldsApiController::class, 'adminList']);
    Route::post('/', [TestsModelFieldsApiController::class, 'create']);
});
