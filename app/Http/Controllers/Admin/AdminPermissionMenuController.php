<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminPermissionMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function getPermissionMenu()
    {
        $params = verify('GET', [
            'admin_permission_id' => 'int|required'
        ]);

        $list = \App\Logic\Admin\AdminPermissionLogic::getAdminPermissMenuionList($params);

        return response_json($list);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function updatePermissionMenu(Request $request, $id)
    {
        $params = verify('POST', [
            'list.*.admin_menu_id' => 'int',
            'is_opt' => 'in:0,1'
        ]);
        $is_opt = $params['is_opt'];
        $list = [];
        if ($is_opt == 1) {
            $list = \App\Logic\Admin\AdminPermissionLogic::addAdminPermissionMenu($params['list'], $id);
        } else {
            \App\Logic\Admin\AdminPermissionLogic::deleteAdminPermissionMenu($params['list'], $id);
        }

        return response_json($list);
    }
}
