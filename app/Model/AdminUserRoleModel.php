<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;


/**
 * Model AdminUserRoleModel
 *
 * 后台管理员角色-权限关联表
 */
class AdminUserRoleModel extends Model
{
    protected $table = 'admin_user_role';
    public $timestamps  = false;
}
