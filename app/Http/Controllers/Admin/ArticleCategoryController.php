<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Taoran\Laravel\Exception\ApiException;

class ArticleCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $validator = Validator::make(request()->query->all(), [
            'name' => '',
            'is_all' => '',
        ]);

        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        $list = \App\Logic\Admin\ArticleCategoryLogic::getArticleCategoryList($validator->validate());

        return response_json([], $list);
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
        $validator = Validator::make(request()->request->all(), [
            'name' => 'required',
            'sort' => 'numeric',
        ]);

        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        \App\Logic\Admin\ArticleCategoryLogic::addArticleCategory($validator->validate());

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

        $data = \App\Logic\Admin\ArticleCategoryLogic::getOneArticleCategory($id);

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
        $validator = Validator::make(request()->request->all(), [
            'name' => 'required',
            'sort' => 'numeric',
        ]);
        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        \App\Logic\Admin\ArticleCategoryLogic::updateArticleCategory($validator->validate(), $id);

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

        \App\Logic\Admin\ArticleCategoryLogic::deleteArticleCategory($id);

        return response_json();
    }
}
