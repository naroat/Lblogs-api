<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AdminUserModel extends Model
{
    protected $table = 'admin_user';

    protected $dateFormat = 'U';

    protected $hidden = ['is_on'];

    /**
     * 用户的角色
     */
    public function roles()
    {
        return $this->belongsToMany(AdminRoleModel::class,'admin_user_role','admin_user_id','admin_role_id');
    }

}
