<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Packages\Core\src\Traits\Response;
use App\Packages\Exception\src\ApiException;
use App\Repositorys\AdminUserRepository;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Taoran\Laravel\Jwt\JwtAuth;

class TestController extends Controller
{
    use Response;
    //
    public function index()
    {
        return $this->responseJson([1 => 1]);

        $re = new AdminUserRepository();
        $res = $re->getAdminUserOne(['id' => 1]);
        dd($res);
        throw new ApiException(222);
        dd(123123);
        $origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';
        dd($origin);
        JwtAuth::set('admin_info', [
            'admin_id' => 1,
            'admin_name' => 'taoran'
        ]);


        dd(JwtAuth::get('admin_info'));
        dd('test');
        throw new ApiException('adsfsaf');
        $jwt = JwtAuth::getInstance();
        $token = $jwt->encode();
        dd($token);
        dd(strtolower(md5(uniqid(mt_rand(), true))));
        $charid = strtolower(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $guid = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) .
            $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
        dd($guid);
    }
}
