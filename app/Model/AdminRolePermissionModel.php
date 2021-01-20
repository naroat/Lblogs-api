<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;


/**
 * Model AdminRolePermissionModel
 *
 * 后台管理员角色权限表
 */
class AdminRolePermissionModel extends Model
{
    protected $table = 'admin_role_permission';
    public $timestamps = false;
}
