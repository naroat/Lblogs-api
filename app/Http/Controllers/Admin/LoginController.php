<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    //登录
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required',
            'password' => 'required'
        ]);

        //验证失败
        if ($validator->fails()) throw new ApiException($validator->errors()->first());

        $data = \App\Services\LoginService::login($request->all());

        return response_json($data);
    }

    //注销
    public function logout(Request $request)
    {
        session()->forget('admin_user');

        return response_json();
    }
}
