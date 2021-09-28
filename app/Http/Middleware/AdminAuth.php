<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Firebase\JWT\JWT;
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
        $sessionPrefix = "token";
        $token = request()->input('token');
        $data = \Cache::get($sessionPrefix . ':' . $token);
        if (empty($data['admin_id'])) {
            throw new ApiException('你还没有登录或登录已过期', 'NO LOGIN');
        }

        return $next($request);
    }
}
