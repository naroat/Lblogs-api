<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Repositorys\AdminUserRepository;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Taoran\Laravel\Exception\ApiException;
use Taoran\Laravel\Jwt\JwtAuth;
use Taoran\Laravel\Upload\Upload;

class TestController extends Controller
{

    public function index(Request $request)
    {
        dd('12313123');
        /*$admin_user = [
            'admin_user' => [
                'admin_id' => 1,
                'admin_name' => 'admin'
            ]
        ];
        session()->put('admin_user', $admin_user);
        session()->save();*/

        dd(session()->get('admin_user'));
        $password = create_password('123123', $salt);
        dd($password);

        dd(ip2long(Request()->getClientIp()));
        return $this->paramVerify();


        dd(123123);
        if (config()) {

        }
        $file_url = request()->getSchemeAndHttpHost();
        dd($file_url);

//        return $file_url;

        $p = '123123';
        dd(encrypt_password($p, 'kPu9r'));
        $filename = 'f8d267922af3cccc63277557e036ea49.jpg';
        return response()->download(storage_path('uploads') . '/' . $filename, $filename);

        $res = (new Upload())->download('f8d267922af3cccc63277557e036ea49.jpg');
        dd($res);
        $param = verify('GET', [
            'username' => 'required'
        ], [
            'username.required' => '请填写用户名称'
        ]);
        dd($param);
        return response_json($param);
        throw new ApiException();
        exit;
        dd(123123);


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

    public function token()
    {
        return 'admin-token';
    }

    public function paramVerify()
    {
        //默认不必填,required:必填, 不传默认字符串
        //string,int,email
        //digits: 指定长度
        //digits_between: 长度范围
        //需要自定义添加: mobile,
        $param = verify('GET', [
            'name' => 'int|required',
            'phone'=> 'mobile',
            'email'=> 'email',
            'start_time' => 'string',
            'end_time' => 'string',
            'is_bind' => 'in:0,1',
            'num' => 'between:1,5|int', //必须添加int, 不然between不生效
            //数组
            'arr.*.id' => 'int',
            'arr.*.name' => '',
        ]);
        return response_json($param);
    }
}
