<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminRolePermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function index()
    {
        $params = verify('GET', [
            'admin_role_id' => 'int|required'
        ]);

        $list = \App\Logic\Admin\AdminRolePermissionLogic::getAdminRolePermissionList($params['admin_role_id']);

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
    public function update(Request $request, $id)
    {
        $params = verify('POST', [
            'list.*.admin_permission_id' => 'int|required',
            'is_opt' => 'in:0,1|required'
        ]);

        $list = [];
        if ($params['is_opt'] == 1) {
            $list = \App\Logic\Admin\AdminRolePermissionLogic::addAdminRolePermission($params['list'], $id);
        } else {
            \App\Logic\Admin\AdminRolePermissionLogic::deleteAdminRolePermission($params['list'], $id);
        }

        return response_json($list);
    }
}
