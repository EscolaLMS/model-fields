<?php

use Illuminate\Support\Facades\Route;
use EscolaLms\ModelFields\Http\Controllers\ModelFieldsApiController;
use EscolaLms\ModelFields\Tests\Http\Controllers\TestsModelFieldsApiController;
use EscolaLms\ModelFields\Tests\TraitTest\Http\Controllers\TestsModelFieldsApiController as TestsTraitModelFieldsApiController;

Route::group(['prefix' => 'api/trait/test-users'], function () {
    Route::get('/', [TestsTraitModelFieldsApiController::class, 'list']);
});

Route::group(['prefix' => 'api/admin/trait/test-users'], function () {
    Route::get('/', [TestsTraitModelFieldsApiController::class, 'adminList']);
    Route::post('/', [TestsTraitModelFieldsApiController::class, 'create']);
});


Route::group(['prefix' => 'api/test-users'], function () {
    Route::get('/', [TestsModelFieldsApiController::class, 'list']);
});

Route::group(['prefix' => 'api/admin/test-users'], function () {
    Route::get('/', [TestsModelFieldsApiController::class, 'adminList']);
    Route::post('/', [TestsModelFieldsApiController::class, 'create']);
});
