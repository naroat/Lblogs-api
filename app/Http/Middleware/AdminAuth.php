<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use Closure;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //管理员身份验证
        $admin_user = session('admin_user');
        if (!isset($admin_user) || empty($admin_user['admin_id'])) {
            throw new \Taoran\Laravel\Exception\ApiException('请先登录');
        }

        return $next($request);
    }
}
