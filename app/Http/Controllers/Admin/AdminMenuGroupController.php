<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminMenuGroupController extends Controller
{
    /**
     * 列表
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function index()
    {
        $params = verify('GET', [
            'menu_id' => 'int',
            'no_page' => 'in:1'
        ]);
        $list = \App\Logic\Admin\AdminMenuGroupLogic::getAdminMenuGroupList($params);

        return response_json($list);
    }

    /**
     * 添加数据
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
            'parent_id' => 'int',
            'order' => 'int'
        ]);

        \App\Logic\Admin\AdminMenuGroupLogic::addAdminMenuGroup($params);

        return response_json();
    }

    /**
     * 显示单项
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function show($id)
    {
        $data = \App\Logic\Admin\AdminMenuGroupLogic::getOneAdminMenuGroup($id);

        return response_json($data);
    }

    /**
     * 更新
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
            'order' => 'int'
        ]);

        \App\Logic\Admin\AdminMenuGroupLogic::updateAdminMenuGroup($params, $id);

        return response_json();
    }

    /**
     * 删除
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function destroy($id)
    {
        \App\Logic\Admin\AdminMenuGroupLogic::deleteAdminMenuGroup($id);

        return response_json();
    }
}
