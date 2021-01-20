<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Model AdminMenuGroupModel
 *
 * 菜单组
 */
class AdminMenuGroupModel extends Model
{
    protected $table = 'admin_menu_group';

    //定义为时间戳
    protected $dateFormat = 'U';

    protected $casts = [
        'id' => 'string',   //把id返回字符串
    ];

    /**
     * 下级菜单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs()
    {
        return $this->hasMany('App\Model\AdminMenuGroupModel', 'parent_id', 'id');
    }

    /**
     * 上级
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent()
    {
        return $this->hasOne(\App\Model\AdminMenuGroupModel::class, 'id', 'parent_id');
    }
}
