<?php
namespace App\Http\Controllers\Admin;

use App\Services\ConfigService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ConfigController extends Controller
{
    protected $config;

    public function __construct(ConfigService $config)
    {
        $this->config = $config;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $list = $this->config->getList([]);

        return response_json($list);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function update(Request $request, $id)
    {
        $param = verify('POST', [
            'value' => ''
        ]);

        $this->config->edit($param, $id);

        return response_json();
    }

    /**
     * 更新关于我们
     */
    public function getAboutUs()
    {

        $data = $this->config->getConfigByCode('about_us');

        return response_json($data);
    }

    /**
     * 更新关于我们
     */
    public function putAboutUs()
    {
        $param = verify('POST', [
            'code' => 'about_us',
            'value' => ''
        ]);

        $this->config->putConfigByCode($param);

        return response_json();
    }

    /**
     * 更新用户协议
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function getUserAgreement()
    {

        $data = $this->config->getConfigByCode('user_agreement');

        return response_json($data);
    }

    /**
     * 更新用户协议
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function putUserAgreement()
    {
        $param = verify('POST', [
            'code' => 'user_agreement',
            'value' => ''
        ]);

        $this->config->putConfigByCode($param);

        return response_json();
    }

    /**
     * 获取系统设置
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBaseSetting()
    {
        $data = $this->config->getBaseSetting([
            'is_all' => 1
        ]);
        return response_json($data);
    }

    /**
     * 更新系统配置
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ApiException
     */
    public function updateBaseSetting()
    {
        $param = verify('POST', [
            'base_setting_title' => '',
            'base_setting_loginbg' => '',
            'base_setting_copyright' => '',
            'base_setting_topnav_color' => '',
            'base_setting_topnav_bg' => '',
            'base_setting_sidebar_color' => '',
            'base_setting_sidebar_bg' => '',
            'base_setting_favicon' => '',
        ]);

        $this->config->updateBaseSetting($param);

        return response_json();
    }
}
