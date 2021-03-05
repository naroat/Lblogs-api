<?php

namespace App\Http\Controllers\Admin;

use App\Rules\Mobile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
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
            'name' => '',
            'phone' => new Mobile(),
            'start_time' => 'timestamp',
            'end_time' => 'timestamp',
        ]);

        $list = \App\Logic\Admin\AdminUserLogic::getAdminUserList($params);

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
            'account' => 'required',
            'password' => 'required',
            'name' => 'required',
            'phone' => new Mobile(),
            'headimg' => '',
            'role_ids.*.id' => 'int'
        ]);

        \App\Logic\Admin\AdminUserLogic::addAdminUser($params);

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
        $data = \App\Logic\Admin\AdminUserLogic::getOneAdminUser($id);

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
            'account' => '',
            'password' => '',
            'name' => '',
            'phone' => new Mobile(),
            'headimg' => 'int',
            'role_ids.*.id' => 'int'
        ]);

        \App\Logic\Admin\AdminUserLogic::updateAdminUser($params, $id);

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
        \App\Logic\Admin\AdminUserLogic::deleteAdminUser($id);

        return response_json();
    }
}
