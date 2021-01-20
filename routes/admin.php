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
Route::post('logins', 'Admin\LoginController@login');
Route::delete('logouts', 'Admin\LoginController@logout');
Route::group(['middleware' => ['AdminAuth']], function () {
    //管理员管理
    Route::resource('users', 'Admin\AdminUserController');
    //角色管理
    Route::resource('roles', 'Admin\AdminRoleController');
    //文章管理
    Route::resource('articles', 'Admin\ArticleController');
    //文章标签管理
    Route::resource('article/tags', 'Admin\ArticleTagController');
    //文章分类管理
    Route::resource('article/categorys', 'Admin\ArticleCategoryController');
});
