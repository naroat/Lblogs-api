<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;


/**
 * Model AdminRoleModel
 *
 * 后台管理员角色表
 */
class AdminRoleModel extends Model
{
    protected $table = 'admin_role';

    //定义为秒时间戳
    protected $dateFormat = 'U';


    //不需要记录created_at或updated_at
    //protected $timestamps = false;

    protected $casts = [
        'id' => 'string',   //把id返回字符串
    ];

    protected $hidden = ['is_on'];


    /**
     * 角色权限
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany('\App\Model\AdminPermissionModel','admin_role_permission','admin_role_id','admin_permission_id');
    }

    /**
     * 角色对应的管理员
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function admins()
    {
        return $this->belongsToMany('\App\Model\AdminUserModel','admin_user_role','admin_role_id','admin_user_id');
    }

}
