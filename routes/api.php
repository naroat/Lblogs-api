<?php

use Illuminate\Http\Request;

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
//Route::any('test', 'Api\TestController@index');

Route::any('test', 'Api\TestController@index');
Route::any('test2', 'Api\TestController@test2');
Route::group(['middleware' => ['JwtAuth']], function () {

});

Route::post('upload', 'Api\UploadController@upload');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
