<?php
namespace App\Logic\Common;

use Carbon\Carbon;

class TokenLogic
{
    /**
     * 获取
     *
     * @param $token
     * @return mixed
     */
    public static function get($token)
    {
        //保存token
        return \Cache::get('token:' . $token);
    }

    /**
     * 设置
     *
     * @param $token
     * @param $data
     * @param $expires_time
     */
    public static function set($token, $data, $expires_time)
    {
        //保存token
        \Cache::put('token:' . $token, $data, Carbon::now()->addMinutes($expires_time));
    }
}
