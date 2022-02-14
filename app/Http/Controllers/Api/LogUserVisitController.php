<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Taoran\Laravel\Exception\ApiException;

class LogUserVisitController extends Controller
{


    public function store(Request $request)
    {
        $validator = Validator::make(request()->request->all(), [
            //'ip' => 'required',
            'path' => 'required',
            'web_title' => '',
        ]);
        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        \App\Logic\Api\LogUserVisitLogic::add($validator->validate());

        return response_json();
    }
}
