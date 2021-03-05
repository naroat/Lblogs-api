<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminMenuController extends Controller
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
            'menu_id' => 'int'
        ]);

        $list = \App\Logic\Admin\AdminMenuLogic::getAdminMenuList($params);

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
            'description' => '',
            'url' => '',
            'level' => 'int|required',
            'parent_id' => 'int|required',
            'order' => 'int|required',
            'menu_group_id' => 'int'
        ]);

        \App\Logic\Admin\AdminMenuLogic::addAdminMenu($params);

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
        $data = \App\Logic\Admin\AdminMenuLogic::getOneAdminMenu($id);

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
            'description' => '',
            'url' => '',
            'parent_id' => 'int',
            'order' => 'int',
            'menu_group_id' => 'int'
        ]);

        \App\Logic\Admin\AdminMenuLogic::updateAdminMenu($params, $id);

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
        \App\Logic\Admin\AdminMenuLogic::deleteAdminMenu($id);

        return response_json();
    }

    /**
     * 获取有链接的菜单
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException]
     */
    public function getMenuList(Request $request)
    {
        $list = \App\Logic\Admin\AdminMenuLogic::getMenuList();

        return response_json($list);
    }
}
