<?php
namespace App\Logic\Api;

use Taoran\Laravel\Exception\ApiException;

class LogUserVisitLogic
{
    public static function add($data)
    {
        $data['ip'] = request()->getClientIp();
        $LogUserVisitModel = new \App\Model\LogUserVisitModel();
        set_save_data($LogUserVisitModel, $data);
        $res = $LogUserVisitModel->save();
        if (!$res) {
            throw new ApiException("参数参数错误！");
        }

        return true;
    }

}
