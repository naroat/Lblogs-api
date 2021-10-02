<?php
namespace App\Http\Controllers\Admin;

use App\Rules\Timestamp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Taoran\Laravel\Exception\ApiException;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function index()
    {
        $validator = Validator::make(request()->query->all(), [
            'article_tag' => '',
            'title' => '',
            'is_show' => 'in:0,1',
            'time.*' => new Timestamp(),
        ]);

        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        $list = \App\Logic\Admin\ArticleLogic::getArticleList($validator->validate());

        return response_json([], $list);
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->request->all(), [
            'title' => 'required',
            'tags.*' => '',
            'is_show' => 'in:0,1|required',
            'content' => 'required',
        ]);
        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        \App\Logic\Admin\ArticleLogic::addArticle($validator->validate());

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
        $data = \App\Logic\Admin\ArticleLogic::getOneArticle($id);

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
            'title' => 'required',
            'tags.*' => '',
            'is_show' => 'in:0,1|required',
            'content' => 'required',
        ]);
        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        \App\Logic\Admin\ArticleLogic::updateArticle($validator->validate(), $id);

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
        \App\Logic\Admin\ArticleLogic::deleteArticle($id);

        return response_json();
    }
}
