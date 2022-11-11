<?php

use Illuminate\Http\Request;

// @TODO: Fix Authentication
Route::group(['prefix' => 'v1'], function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/businesses', 'Syngenta\API\BusinessesController@getBusinesses');
    Route::get('/categories', 'Syngenta\API\CategoriesController@getCategories');
    Route::get('/diseases', 'Syngenta\API\DiseasesController@getDiseases');
    Route::get('/regions', 'Syngenta\API\RegionsController@getRegions');
    Route::get('/crops', 'Syngenta\API\CropController@getCrops');
    Route::post('/content', 'Syngenta\API\ContentController@getContent');
    Route::get('/content/last-updated', 'Syngenta\API\ContentController@getLatestUpdatedContent');

});
