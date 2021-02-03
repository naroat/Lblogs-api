<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;


/**
 * Model ConfigModel
 *
 * 配置表 - 模型
 */
class ConfigModel extends Model
{
    protected $table = 'config';

    //定义为秒时间戳
    protected $dateFormat = 'U';


    //不需要记录created_at或updated_at
    //protected $timestamps = false;

    protected $casts = [
        'id' => 'string',   //把id返回字符串
    ];
}
