<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminPermissionController extends Controller
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
            'permission_id' => 'int'
        ]);
        $list = \App\Logic\Admin\AdminPermissionLogic::getAdminPermissionList($params);

        return response_json($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function store(Request $request)
    {
        $params = verify('POST', [
            'name' => 'required',
            'code' => '',
            'description' => '',
            'parent_id' => 'int',
            'level' => 'in:1,2'
        ]);

        \App\Logic\Admin\AdminPermissionLogic::addAdminPermission($params);

        return response_json();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function show($id)
    {
        $data = \App\Logic\Admin\AdminPermissionLogic::getOneAdminPermission($id);

        return response_json($data);
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
            'name' => '',
            'code' => '',
            'description' => '',
            'parent_id' => 'int',
            'level' => 'in:1,2',
            'is_auth' => 'in:0,1'
        ]);

        \App\Logic\Admin\AdminPermissionLogic::udpateAdminPermission($params, $id);

        return response_json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function destroy($id)
    {
        \App\Logic\Admin\AdminPermissionLogic::deleteAdminPermission($id);

        return response_json();
    }
}
