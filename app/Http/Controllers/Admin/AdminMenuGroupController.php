<?php
namespace App\Http\Controllers\Admin;

use App\Services\AdminMenuGroupService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminMenuGroupController extends Controller
{
    protected $adminMenuGroup;

    public function __construct(AdminMenuGroupService $adminMenuGroup)
    {
        $this->adminMenuGroup = $adminMenuGroup;
    }

    /**
     * 列表
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function index()
    {
        $list = $this->adminMenuGroup->getList([]);

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
        $param = verify('POST', [
            'name' => 'required',
            'description' => '',
            'url' => '',
            'parent_id' => 'int|required',
            'order' => 'int|required'
        ]);

        $this->adminMenuGroup->add($param);

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
        $this->adminMenuGroup->getOne($id);

        return response_json();
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
        $param = verify('POST', [
            'name' => '',
            'description' => '',
            'url' => '',
            'order' => 'int'
        ]);

        $this->adminMenuGroup->edit($param, $id);

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
        $this->verifyId($id);

        $this->adminMenuGroup->delete($id);

        return response_json();
    }
}
