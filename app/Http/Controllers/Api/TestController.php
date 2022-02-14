<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Taoran\Laravel\Exception\ApiException;

class TestController extends Controller
{
    public function index()
    {
        dd(config('app.timezone'));
        dd(get_msectime());
        $token = JWT::encode([
            'user' => 'taoarn',
            'age' => '28',
        ], config('app.key'));
        dd($token);

        \Jwt::set('test', '111');
        dd(123);
        dd(session('admin_user'));
    }

    public function test2()
    {
        //解析
        $parse = (array)JWT::decode("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhZG1pbl9pZCI6MSwiYWRtaW5fbmFtZSI6ImFkbWluIiwicm9sZXMiOiJcdThkODVcdTdlYTdcdTdiYTFcdTc0MDZcdTU0NTgsIn0.fuwCh8goWzfXRkML__RDr49wjL9UagFOA3Jn-0l9rBc", config('app.key'), array('HS256'));

        if (empty($parse['admin_id'])) {
            throw new ApiException('你还没有登录或登录已过期', 'NO LOGIN');
        }

        dd($parse);
//        $res = Cache::get("token:" . $parse['session_key']);
        //        $res = \Jwt::get('test');
//        dd($res);
    }
}
