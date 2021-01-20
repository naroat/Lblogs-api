<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;


/**
 * Model AdminMenuModel
 *
 * 管理员-菜单表 - 模型
 */
class AdminMenuModel extends Model
{
    protected $table = 'admin_menu';

    //定义为秒时间戳
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
        return $this->hasMany(\App\Model\AdminMenuModel::class, 'parent_id', 'id');
    }

    /**
     * 栏目
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function menuGroup()
    {
        return $this->hasOne(\App\Model\AdminMenuGroupModel::class, 'id', 'menu_group_id');
    }

}
