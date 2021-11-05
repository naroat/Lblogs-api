<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;

class NavCategoryModel extends Model
{
    protected $table = 'nav_category';

    protected $dateFormat = 'Uv';

    protected $hidden = ['is_on'];

    protected $casts = [
    ];

    const IS_SHOW = [
        0 => '隐藏',
        1 => '显示',
    ];

    //没有该方法会导致读取出来的时间戳和数据库不同，前台显示的时间就会错误
    public function asDateTime($value)
    {
        if (is_numeric($value)) {
            return Date::createFromTimestampMs($value);
        }

        return parent::asDateTime($value);
    }

    /**
     * 获取created_at字段时处理
     * @param $value
     * @return int
     */
    public function getCreatedAtAttribute($value)
    {
        return (int)$value;
    }


    /**
     * 获取updated_at字段时处理
     * @param $value
     * @return int
     */
    public function getUpdatedAtAttribute($value)
    {
        return (int)$value;
    }

    /**
     * 导航分类
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function navs()
    {
        return $this->belongsToMany(\App\Model\NavModel::class, 'nav_nav_category', 'nav_category_id', 'nav_id');
    }
}
