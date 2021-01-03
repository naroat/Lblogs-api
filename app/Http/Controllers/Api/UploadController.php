<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Taoran\Laravel\Exception\ApiException;
use Taoran\Laravel\Upload\Upload;

class UploadController extends Controller
{
    /**
     * 文件上传
     *
     * @return mixed
     */
    public function upload()
    {
        $params = verify('POST', [
            'upload_type' => 'required'
        ], [
            'upload_type.required' => '上传类型错误!'
        ]);

        $upload = new Upload();

        if (config('upload.drive') == 'aliyun') {
            //通过上传类型设置存储信息
            $this->setPathByUploadType($params['upload_type'], $upload);
        }

        $path = $upload->upload();
        return response_json([
            'path' => $path
        ]);
    }

    /**
     * 通过上传类型设置存储路径
     *
     * @param $upload_type
     * @param $upload
     * @throws ApiException
     */
    public function setPathByUploadType($upload_type, $upload)
    {
        switch ($upload_type) {
            case 'temp_img':
                $upload->path = 'temp/img';
                $upload->acl = 'public';    //设置访问权限
                break;
            case 'public_img':
                $upload->path = 'public/img';
                $upload->acl = 'public';    //设置访问权限
                break;
            case 'article_cover':
                $upload->path = 'article/cover';
                $upload->acl = 'public';    //设置访问权限
                break;
            default:
                throw new ApiException('上传类型错误');
                break;
        }
    }
}
