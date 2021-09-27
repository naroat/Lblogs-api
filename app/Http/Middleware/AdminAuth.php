<?php

namespace App\Http\Middleware;

use Taoran\Laravel\Exception\ApiException;
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
        if (config('app.debug') === true && $request->query->get('usertest') && $request->query->get('usertest') > 0) {
            $role = [];
            $role_info = \App\Model\AdminUserModel::find($request->query->get('usertest'))->roles()->get(['admin_role_id', 'name']);

            if(!$role_info ->isEmpty()){
                foreach ($role_info as $v) {
                    $role[]=$v->admin_role_id;
                }
            }

            \Jwt::set('admin_info', array(
                'admin_id' => $request->query->get('usertest'),
                'role' => $role
            ));
        }

        if (!(!empty(\Jwt::get('admin_info')) && !empty(\Jwt::get('admin_info.admin_id')))) {
            throw new ApiException('你还没有登录或登录已过期', 'NO LOGIN');
        }

        return $next($request);
    }
}
