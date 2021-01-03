<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Taoran\Laravel\Exception\ApiException;

class LoginService
{
    public static function login($data)
    {
        $admin_user = \App\Model\AdminUserModel::select(['id', 'account', 'password', 'salt'])
            ->where('account', $data['account'])
            ->first();
        if (!$admin_user) {
            //账号不存在
            throw new ApiException("账号或密码不正确！");
        }
        if (encrypt_password($data['password'], $admin_user->salt) != $admin_user->password) {
            //密码错误
            throw new ApiException("账号或密码不正确！");
        }

        //登录成功
        $admin_user = [
            'admin_user' => [
                'admin_id' => $admin_user->id,
                'admin_name' => $admin_user->account
            ]
        ];
        session()->put('admin_user', $admin_user);
        session()->save();

        return $admin_user;
    }
}
