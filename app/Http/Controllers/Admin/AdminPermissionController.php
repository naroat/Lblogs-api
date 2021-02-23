<?php
namespace App\Http\Controllers\Admin;

use App\Services\AdminPermissionService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminPermissionController extends Controller
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
    public function index()
    {
        $params = verify('GET', [
            'permission_id' => 'int'
        ]);

        $list = $this->adminPermission->getAdminPermissionList($params);

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
            'parent_id' => 'int|required',
            'level' => 'int|required',
            'is_auth' => 'int'
        ], 'POST');

        $this->adminPermission->addAdminPermission($params);

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
        $data = $this->adminPermission->getOneAdminPermission($id);

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
            'level' => 'int',
            'is_auth' => 'int'
        ], 'POST');


        $this->adminPermission->udpateAdminPermission($this->verifyData, $id);

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
        $this->adminPermission->deleteAdminPermission($id);

        return response_json();
    }
}
