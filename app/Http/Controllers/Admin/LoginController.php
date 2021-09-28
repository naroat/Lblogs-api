<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Taoran\Laravel\Exception\ApiException;

class LoginController extends Controller
{
    //登录
    public function login(Request $request)
    {
        $validator = Validator::make($request->post(), [
            'account' => 'required',
            'password' => 'required'
        ]);
        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        $data = \App\Logic\Admin\LoginLogic::login($request->all());
        return response_json($data);
    }

    //注销
    public function logout(Request $request)
    {
        \Cache::forget('token:'. request()->input('token'));

        return response_json();
    }
}
