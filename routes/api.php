<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/me', 'Me\MeController@me');

Route::get('/selects', 'Select\SelectController@lists');

Route::group(['prefix' => 'customers', 'namespace' => 'Customer'], function () {
    Route::group(['prefix' => 'photoshoots'], function () {
        Route::get('/', 'PhotoshootController@lists')->name('customers.photoshoot.lists');
        Route::post('/', 'PhotoshootController@store');
        Route::patch('/{photoshoot}', 'PhotoshootController@update');
        Route::patch('/{photoshoot}/photoshoot-assets/{photoshoot_asset}', 'PhotoshootController@updateAssetStatus');
    });
});

Route::group(['prefix' => 'photographers', 'namespace' => 'Photographer'], function() {
    Route::group(['prefix' => 'photoshoots'], function () {
        Route::get('/', 'PhotoshootController@lists');
        Route::post('/{photoshoot}/assets', 'PhotoshootController@addUpload');
        Route::post('/{photoshoot}/photoshoot-assets/{photoshoot_asset}', 'PhotoshootController@updatePhotoshootAsset');
    });
});

Route::group(['prefix' => 'admins', 'namespace' => 'Admin'], function() {
    Route::group(['prefix' => 'photoshoots'], function () {
        Route::get('/', 'PhotoshootController@lists');
    });
});
