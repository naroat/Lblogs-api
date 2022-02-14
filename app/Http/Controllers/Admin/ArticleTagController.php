<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleTagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function index()
    {
        $this->verify([
            'name' => 'no_required',
            'is_page' => 'no_required|in:0,1'
        ], 'GET');
        $data = $this->verifyData;
        $list = \App\Logic\Admin\ArticleTagLogic::getArticleTagList($data);

        return $this->responseList($list);
    }

    /**
     * Store a newly created resource in storage.
     *ß
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function store(Request $request)
    {
        $this->verify([
            'name' => ''
        ], 'POST');

        \App\Logic\Admin\ArticleTagLogic::addArticleTag($this->verifyData);

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

        $data = \App\Logic\Admin\ArticleTagLogic::getOneArticleTag($id);

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

        \App\Logic\Admin\ArticleTagLogic::updateArticleTag($this->verifyData, $id);

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

        \App\Logic\Admin\ArticleTagLogic::deleteArticleTag($id);

        return $this->response();
    }
}
