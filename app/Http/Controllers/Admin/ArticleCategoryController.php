<?php

namespace App\Http\Controllers\Admin;

use Taoran\Laravel\Exception\ApiException;
use App\Http\Controllers\Controller;
use App\Services\ArticleCategoryService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class ArticleCategoryController extends Controller
{
    protected $articleCategoryService;

    public function __construct(ArticleCategoryService $articleCategoryService)
    {
        $this->articleCategoryService = $articleCategoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $list = $this->articleCategoryService->getList();

        return response_json($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $param = $this->storeVerify($request);

        $this->articleCategoryService->add($param);

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
        $data = $this->articleCategoryService->getOne($id);
        return response_json($data);
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
        $param = $this->storeVerify($request);

        $this->articleCategoryService->update($param, $id);

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
        $this->articleCategoryService->delete($id);

        return response_json();
    }

    //验证
    public function storeVerify($request)
    {
        $param['name'] = $request->get('name');
        //$param['parent_id'] = $request->get('parent_id');

        $rule = [
            'name' => 'required|max:20',
        ];

        $message = [
            'name.required' => '请填写分类名称！',
            'name.max' => '分类名称长度超过20'
        ];

        $validator = Validator::make($param, $rule, $message);

        //验证失败
        if ($validator->fails()) {
            throw new ApiException($validator->errors()->first());
        }

        return $param;
    }
}
