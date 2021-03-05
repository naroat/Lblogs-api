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
//获取菜单
Route::get('get/menus', 'Admin\AdminMenuAvailableController@index');
Route::group(['middleware' => ['AdminAuth']], function () {
    //栏目管理
    Route::resource('menu/groups', 'Admin\AdminMenuGroupController');
    //菜单管理
    Route::resource('menus', 'Admin\AdminMenuController');
    //权限管理
    Route::resource('permissions', 'Admin\AdminPermissionController');
    //角色管理
    Route::resource('roles', 'Admin\AdminRoleController');
    //权限绑定角色
    Route::resource('role/permissions', 'Admin\AdminRolePermissionController');
    //权限绑定菜单栏目
    Route::get('permission/menus', 'Admin\AdminPermissionMenuController@getPermissionMenu');
    Route::put('permission/menus/{id}', 'Admin\AdminPermissionMenuController@updatePermissionMenu');
    Route::get('permission/menu/groups', 'Admin\AdminPermissionMenuGroupController@getPermissionMenuGroup');
    Route::put('permission/menu/groups/{id}', 'Admin\AdminPermissionMenuGroupController@updatePermissionMenuGroup');

    //管理员管理
    Route::resource('users', 'Admin\AdminUserController');

    //文章管理
    Route::resource('articles', 'Admin\ArticleController');
    //文章标签管理
    Route::resource('article/tags', 'Admin\ArticleTagController');
    //文章分类管理
    Route::resource('article/categorys', 'Admin\ArticleCategoryController');
    //表单管理
    Route::resource('forms', 'Admin\FormController');
    //系统设置
//    Route::get('configs', 'Admin\ConfigController');
//    Route::put('configs/{id}', 'Admin\ConfigController');
    //关于我们 - 查看

    //关于我们 - 编辑

});
