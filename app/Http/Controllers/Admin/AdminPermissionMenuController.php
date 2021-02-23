<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AdminPermissionService;

class AdminPermissionMenuController extends Controller
{
    protected $adminPermission;

    public function __construct(AdminPermissionService $adminPermission)
    {
        $this->adminPermission = $adminPermission;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function getPermissionMenu()
    {
        $params = verify('GET', [
            'admin_permission_id' => 'int'
        ]);

        $list = $this->adminPermission->getAdminPermissMenuionList($params['admin_permission_id']);

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
            //'is_opt' => 'in:0,1'
            'is_opt' => 'int'
        ]);

        $data = $this->verifyData;

        $list = [];
        if ($data['is_opt'] == 1) {
            $list = $this->adminPermission->addAdminPermissionMenu($params, $id);
        } else {
            $this->adminPermission->deleteAdminPermissionMenu($params, $id);
        }

        return response_json($list);
    }
}
