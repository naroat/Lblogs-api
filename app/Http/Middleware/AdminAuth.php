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


        return $next($request);
    }
}
