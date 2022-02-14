<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Taoran\Laravel\Exception\ApiException;

class FeedbackController extends Controller
{
    /**
     * 留言
     */
    public function store()
    {
        $validator = Validator::make(request()->request->all(), [
            'email' => 'required',
            'content' => 'required',
        ]);

        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        \App\Logic\Api\FeedbackLogic::add($validator->validate());

        return response_json();
    }
}
