<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Rules\Timestamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Taoran\Laravel\Exception\ApiException;

class NavCategoryController extends Controller
{
    public function index()
    {
        $validator = Validator::make(request()->query->all(), [
            'title' => '',
            'is_all' => '',
        ]);

        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        $list = \App\Logic\Admin\NavCategoryLogic::getList($validator->validate());

        return response_json([], $list);
    }

    public function show($id)
    {
        $data = \App\Logic\Admin\NavCategoryLogic::getOne($id);

        return response_json($data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(request()->request->all(), [
            'title' => 'required',
            'sort' => 'numeric',
        ]);

        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        \App\Logic\Admin\NavCategoryLogic::add($validator->validate());

        return response_json();
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make(request()->request->all(), [
            'title' => 'required',
            'sort' => 'numeric',
        ]);
        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        \App\Logic\Admin\NavCategoryLogic::update($validator->validate(), $id);

        return response_json();
    }

    public function destroy($id)
    {
        \App\Logic\Admin\NavCategoryLogic::delete($id);

        return response_json();
    }
}
