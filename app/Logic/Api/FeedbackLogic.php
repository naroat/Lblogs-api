<?php
namespace App\Logic\Api;

use Illuminate\Support\Facades\Validator;
use Taoran\Laravel\Exception\ApiException;

class FeedbackLogic
{
    /**
     * 添加留言
     *
     * @param $data
     * @return bool
     * @throws ApiException
     */
    public static function add($data)
    {
        $feedback = new \App\Model\FeedbackModel();
        set_save_data($feedback, $data);
        $res = $feedback->save();
        if (!$res) {
            throw new ApiException();
        }
        return true;
    }
}
