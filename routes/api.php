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

//获取文章列表
Route::get('articles', 'Api\ArticleController@index');
//获取文章列表详情
Route::get('articles/{id}', 'Api\ArticleController@show');
//获取文章归档
Route::get('article/archives', 'Api\ArticleController@Archive');
//添加留言
Route::post('feedbacks', 'Api\FeedbackController@store');
//获取导航
Route::get('navs', 'Api\NavController@index');

