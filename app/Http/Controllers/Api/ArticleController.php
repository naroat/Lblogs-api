<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Taoran\Laravel\Exception\ApiException;

class ArticleController extends Controller
{
    public function index() {

        $validator = Validator::make(request()->query->all(), [
            'title' => ''
        ]);

        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        $list = \App\Logic\Api\ArticleLogic::getList($validator->validate());

        return response_json([], $list);
    }

    public function show($id) {

        $data = \App\Logic\Api\ArticleLogic::getOne($id);

        return response_json($data);
    }
}
