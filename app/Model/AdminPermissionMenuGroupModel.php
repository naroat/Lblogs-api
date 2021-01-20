<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Model AdminPermissionMenuGroupModel
 *
 * 后台管理权限栏目-关联表 - 模型
 */
class AdminPermissionMenuGroupModel extends Model
{

    protected $table = 'admin_permission_menu_group';

    //定义为时间戳
    //protected $dateFormat = 'U';

    //不需要记录created_at或updated_at
    public $timestamps = false;

    protected $casts = [
        'id' => 'string',   //把id返回字符串
    ];

}
