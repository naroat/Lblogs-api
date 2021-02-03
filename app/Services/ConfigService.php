<?php


namespace App\Services;

use App\Repositorys\ConfigRepository;
use Taoran\Laravel\Exception\ApiException;

class ConfigService
{
    protected $configRepository;

    public function __construct()
    {
        $this->configRepository = new ConfigRepository();
    }

    public function getList($param)
    {
        return $this->configRepository->getList($param);
    }

    public function getOne($id)
    {
        $data = $this->configRepository->getOneById($id);
        if (!$data) {
            throw new ApiException('数据不存在!');
        }
        return $data;
    }

    public function edit($param, $id)
    {
        if (empty($param)) {
            throw new ApiException('你没有做任何修改');
        }

        $res  =$this->configRepository->getOneById($id);
        if (!$res) {
            throw new ApiException('数据不存在!');
        }

        //更新
        $this->configRepository->update($res, $param);

        return true;
    }

    /**
     * 通过code获取配置信息
     *
     * @param $code
     * @return mixed
     * @throws ApiException
     */
    public function getConfigByCode($code)
    {
        $data = $this->configRepository->getOne(function ($query) use ($code) {
            $query->where('code', $code);
        });
        if (!$data) {
            throw new ApiException('配置信息不存在!');
        }
        return $data;
    }

    /**
     * 通过code修改配置信息
     *
     * @param $param
     * @return mixed
     * @throws ApiException
     */
    public function putConfigByCode($param)
    {
        $data = $this->configRepository->getOne(function ($query) use ($param) {
            $query->where('code', $param['code']);
        });
        if (!$data) {
            throw new ApiException('配置信息不存在!');
        }
        $data->value = $param['value'] ?? '';
        $res = $data->save();
        if (!$res) {
            throw new ApiException();
        }
        return $data;
    }

    /**
     * 获取基础配置
     */
    public function getBaseSetting($param)
    {
        $data = $this->configRepository->getList($param, function ($query) {
            $query->select([
                'base_setting_title',
                'base_setting_loginbg',
                'base_setting_copyright',
                'base_setting_topnav_color',
                'base_setting_topnav_bg',
                'base_setting_sidebar_color',
                'base_setting_sidebar_bg',
                'base_setting_favicon',
            ]);
        });
        return $data;
    }

    /**
     * 更新基础设置
     *
     * @param $param
     */
    public function updateBaseSetting($param)
    {
        foreach ($param as $k => $v) {
            $data = $this->configRepository->getOne(function ($query) use ($k, $v) {
                $query->where('code', $k);
            });
            if (!$data) {
                continue;
            }
            //更新
            $data->$k = $v;
            $data->save();
        }
        return true;
    }
}
