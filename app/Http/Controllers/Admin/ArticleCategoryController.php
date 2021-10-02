<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $list = \App\Logic\Admin\ArticleCategoryLogic::getArticleCategoryList();

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
        $this->verify([
            'name' => '',
            'parent_id' => 'no_required|egnum'
        ], 'POST');

        \App\Logic\Admin\ArticleCategoryLogic::addArticleCategory($this->verifyData);

        return $this->response();
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
        $this->verifyId($id);

        $data = \App\Logic\Admin\ArticleCategoryLogic::getOneArticleCategory($id);

        return $this->response($data);
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
        $this->verifyId($id);
        $this->verify([
            'name' => 'no_required'
        ], 'POST');

        \App\Logic\Admin\ArticleCategoryLogic::updateArticleCategory($this->verifyData, $id);

        return $this->response();
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
        $this->verifyId($id);

        \App\Logic\Admin\ArticleCategoryLogic::deleteArticleCategory($id);

        return $this->response();
    }
}
