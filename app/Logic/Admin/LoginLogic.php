<?php
namespace App\Logic\Admin;

use App\Logic\Common\TokenLogic;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Taoran\Laravel\Exception\ApiException;

class LoginLogic
{
    public static function login($data)
    {
        //登录验证
        $admin_user = self::auth($data, $role, $role_name);

        //生成token
        $encode_data = [
            'admin_id' => $admin_user->id,
            'admin_name' => $admin_user->account,
            'roles' => $role_name,
        ];

        //过期时间
        $expires_time = 60;
        $time = time();
        $encode_data['expires_time'] = $time + ($expires_time * 60);
        $encode_data['refresh_time'] = $time;

        $admin_user->token = JWT::encode($encode_data, config('app.key'));
        $save_res = $admin_user->save();
        if (!$save_res) {
            throw new ApiException("数据库错误!");
        }

        TokenLogic::set($admin_user->token, $encode_data, $encode_data['expires_time']);
        return [
            'admin_id' => $admin_user->id,
            'admin_name' => $admin_user->account,
            'roles' => $role_name,
            'token' => $admin_user->token
        ];;
    }

    /**
     * 登录验证
     *
     * @param $data
     * @return mixed
     * @throws ApiException
     */
    public static function auth($data, &$role, &$role_name)
    {
        $admin_user = \App\Model\AdminUserModel::select(['id', 'account', 'password', 'salt', 'token'])
            ->where('account', $data['account'])
            ->first();
        if (!$admin_user) {
            //账号不存在
            throw new ApiException("管理员不存在！");
        }
        if (!eq_password($admin_user->password, $data['password'], $admin_user->salt)) {
            //密码错误
            throw new ApiException("账号或密码不正确！");
        }

        $role = [];
        $role_name = '';
        $role_info = $admin_user->roles()->get(['admin_role_id', 'name']);
        if (!$role_info->isEmpty()) {
            foreach ($role_info as $v) {
                $role[] = $v->admin_role_id;
                $role_name .= $v['name'] . ',';
            }
            trim($role_name, ',');
        }

        return $admin_user;
    }
}
