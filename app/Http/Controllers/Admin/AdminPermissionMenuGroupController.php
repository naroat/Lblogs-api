<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminPermissionMenuGroupController extends Controller
{

    /**
     * 获取当前权限绑定的栏目
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function getPermissionMenuGroup()
    {
        $params = verify('GET', [
            'admin_permission_id' => 'int|required'
        ]);

        $list = \App\Logic\Admin\AdminPermissionMenuGroupLogic::getAdminPermissMenuionGroupList($params['admin_permission_id']);

        return response_json($list);
    }

    /**
     * 更新当前权限绑定的栏目
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function updatePermissionMenuGroup(Request $request, $id)
    {
        $params = verify('POST', [
            'admin_menu_group_id' => 'int|required',
            'is_opt' => 'in:0,1|required'
        ]);

        if ($params['is_opt'] == 1) {
            \App\Logic\Admin\AdminPermissionMenuGroupLogic::addAdminPermissionMenuGroup($params, $id);
        } else {
            \App\Logic\Admin\AdminPermissionMenuGroupLogic::deleteAdminPermissionMenuGroup($params, $id);
        }

        return response_json();
    }
}
