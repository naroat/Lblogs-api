<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params = verify('GET', [
            'title' => ''
        ]);
        $list = \App\Logic\Admin\FormLogic::getList($params);
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
        $params = verify('POST', [
            'title' => 'required',
            'description' => '',
            'element.*.type' => 'int',
            'element.*.title' => '',
            'element.*.name' => '',
            'element.*.is_must' => 'in:0,1',
            'element.*.sort' => 'int',
            'element.*.options.*.value' => '',
        ]);

        \App\Logic\Admin\FormLogic::addForm($params);
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
        $data = \App\Logic\Admin\FormLogic::getOne($id);
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
        $params = verify('POST', [
            'title' => '',
            'description' => '',
            'element.*.type' => 'int',
            'element.*.title' => '',
            'element.*.name' => '',
            'element.*.is_must' => 'in:0,1',
            'element.*.sort' => 'int',
            'element.*.options.*.value' => '',
        ]);

        \App\Logic\Admin\FormLogic::updateForm($params, $id);
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
        \App\Logic\Admin\FormLogic::deleteForm($id);
        return response_json();
    }
}
