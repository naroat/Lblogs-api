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
    //栏目管理
    Route::resource('menu/groups', 'Admin\AdminMenuGroupController');
    //菜单管理
    Route::resource('menus', 'Admin\AdminMenuController');
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
    //系统设置
//    Route::get('configs', 'Admin\ConfigController');
//    Route::put('configs/{id}', 'Admin\ConfigController');
    //关于我们 - 查看

    //关于我们 - 编辑

});
