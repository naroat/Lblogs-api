<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Model AdminPermissionModel
 *
 * 后台管理权限表 - 模型
 */
class AdminPermissionModel extends Model
{
    use CoreModel;

    protected $table = 'admin_permission';

    //定义为秒时间戳
    protected $dateFormat = 'U';

    protected $casts = [
        'id' => 'string',   //把id返回字符串
    ];

}
