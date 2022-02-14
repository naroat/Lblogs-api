<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Model AdminPermissionMenuModel
 *
 * 后台管理权限菜单-关联表 - 模型
 */
class AdminPermissionMenuModel extends Model
{
    protected $table = 'admin_permission_menu';

    public $timestamps = false;

    protected $hidden = ['is_on'];
}
