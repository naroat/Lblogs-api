<?php
namespace App\Logic\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Taoran\Laravel\Exception\ApiException;

class LoginLogic
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
        if (!eq_password($admin_user->password, $data['password'], $admin_user->salt)) {
            //密码错误
            throw new ApiException("账号或密码不正确！");
        }

        $role = [];
        $role_info = $admin_user->roles()->get(['admin_role_id', 'name']);
        if (!$role_info->isEmpty()) {
            foreach ($role_info as $v) {
                $role[] = $v->admin_role_id;
            }
        }

        //保存session
        session()->put('admin_user', [
            'admin_id' => $admin_user->id,
            'role' => $role,
        ]);
        session()->save();

        return [
            'admin_id' => $admin_user->id,
            'admin_name' => $admin_user->account,
            'roles' => $role_info
        ];;
    }
}
