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

        $data = \App\Services\LoginService::login($request->all());
        $data['token'] = 'admin_token';
        return response_json($data, 200);
    }

    //注销
    public function logout(Request $request)
    {
        session()->forget('admin_user');

        return response_json();
    }
}
