<?php

namespace App\Http\Middleware;


use Taoran\Laravel\Exception\ApiException;
use Closure;

class JwtAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws ApiException
     */
    public function handle($request, Closure $next)
    {
        if (!\Jwt::check($request, '')) {
            throw new ApiException('AUTHORIZATION验证失败', 'AUTHORIZATION_INVALID', 401);
        }

        if (config('app.debug') === true && $request->query->get('usertest') && $request->query->get('usertest') > 0) {
            \Jwt::set('user_info', array(
                'user_id' => $request->query->get('usertest')
            ));
        }

        return $next($request);
    }


}
