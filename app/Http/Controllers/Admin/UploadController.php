<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Taoran\Laravel\Exception\ApiException;
use Taoran\Laravel\Upload\Upload;

class UploadController extends Controller
{
    public function uploadBase64(Request $request)
    {
        //保存到本地临时文件
        $upload_path = storage_path('app/upload/markdown/' . date('Ymd', time()) . '/');

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        $file_param = $request->all()['file'];
        $ext_explode =  explode('.', $file_param['_name']);
        $ext = '.' . $ext_explode[count($ext_explode) - 1];
        $filename = get_msectime() . $ext;

        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $file_param['miniurl'], $result)) {
            if (!file_put_contents($upload_path . $filename, base64_decode(str_replace($result[1], '', $file_param['miniurl'])))) {
                throw new ApiException('上传失败！');
            }
        }

        $path = $upload_path . $filename;
        if (config('upload.drive') == 'aliyun') {
            //上传到oss - 这里情况比较特殊，不使用upload类
            $uploadAli = new \Taoran\Laravel\Upload\Aliyun\Upload();
            $up_path = 'markdown/';
            $uploadAli->uploadDirect($up_path . $filename, $path, 'public');
            $path = config('aliyun.oss.bucket_domain') . '/' . $up_path . $filename;
        }

        return response_json([
            'path' => $path
        ]);
    }
}
