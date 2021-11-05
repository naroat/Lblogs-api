<?php
namespace App\Logic\Api;

class NavLogic
{
    public static function getList()
    {
        $list = \App\Model\NavCategoryModel::where('is_on', 1)
            ->with(['navs' => function ($query) {
                $query->where('is_on', 1);
            }])
            ->get();

        $list->each(function ($item, $key) use (&$list) {
            //过滤没有导航的分类
            if ($item->navs->isEmpty()) {
                unset($list[$key]);
            }
        });

        return $list;
    }

}
