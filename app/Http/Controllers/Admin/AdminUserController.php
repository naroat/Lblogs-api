<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Rules\Mobile;
use App\Rules\Timestamp;
use App\Services\AdminUserService;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    protected $adminUser;

    public function __construct(AdminUserService $adminUser)
    {
        $this->adminUser = $adminUser;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $param = verify('GET', [
            'name' => '',
            'phone'=>'int',
            'start_time' => new Timestamp(),
            'end_time' => new Timestamp(),
        ]);
        $list = $this->adminUser->getAdminUserList($param);
        return response_json($list);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $param = verify('POST', [
            'account' => 'required',
            'password' => 'required',
            'name' => 'required',
            'phone' => new Mobile(),
            'headimg' => '',
            'role_ids.*.id' => 'int|required'
        ]);
        $this->adminUser->addAdminUser($param);
        return response_json();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->adminUser->getAdminUserOne($id);
        return response_json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $param = verify('POST', [
            'account' => '',
            'password' => '',
            'name' => '',
            'phone' => new Mobile(),
            'headimg' => '',
            'role_ids.*.id' => 'int'
        ]);

        $this->adminUser->updateAdminUser($id, $param);
        return response_json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->adminUser->deleteAdminUser($id);
        return response_json();
    }
}
