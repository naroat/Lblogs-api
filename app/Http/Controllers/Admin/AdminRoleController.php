<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositorys\AdminRoleRepository;
use App\Services\AdminRoleService;
use Illuminate\Http\Request;

class AdminRoleController extends Controller
{
    protected $adminRole;

    public function __construct(AdminRoleService $adminRole)
    {
        $this->adminRole = $adminRole;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = $this->adminRole->getList();
        return response_json($list);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'name' => 'required',
            'description' => 'required'
        ]);

        $this->adminRole->addAdminRole($param);

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
        $data = $this->adminRole->getOneAdminRole($id);
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
            'name' => '',
            'description' => ''
        ]);
        $this->adminRole->updateAdminRole($param, $id);
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
        $this->adminRole->deleteAdminRole($id);
        return response_json();
    }
}
