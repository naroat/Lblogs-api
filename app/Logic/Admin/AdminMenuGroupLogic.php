<?php

namespace App\Logic\Admin;

use App\Exceptions\ApiException;
use App\Exceptions\DatabaseException;

class AdminMenuGroupLogic
{

    /**
     * 列表
     * @param array $data 列表查询条件
     *              $data.
     * @return
     */
    public static function getAdminMenuGroupList($data)
    {
        $list = \App\Model\AdminMenuGroupModel::where('is_on', '=', 1)->orderBy('order');

        $res = array();

        if (isset($data['menu_id'])) {
            $res = \App\Model\AdminMenuGroupModel::where('is_on', '=', 1)
                ->select(['id', 'name'])
                ->find($data['menu_id']);

            $list = $list->where('parent_id', '=', $data['menu_id'])
                ->paginate(15);
        } else {
            $list->where('parent_id', '=', 0)
                ->with(['childs' => function ($query) {
                    $query->where('is_on', 1)->select(['id', 'parent_id', 'name', 'level']);
                }]);

            if (isset($data['no_page']) && $data['no_page'] == 1) {
                $list = $list->get();
            } else {
                $list = $list->paginate(15);
            }

            $list->each(function ($item) {
                if ($item->childs->isEmpty()) {
                    $item->is_childs = 0;
                } else {
                    $item->is_childs = 1;
                }
            });
        }

        return [
            'data' => $res,
            'list' => $list
        ];
    }

    /**
     * 获取详情
     * @param int $id ID
     * @return
     * @throws ApiException
     */
    public static function getOneAdminMenuGroup($id)
    {
        $data = \App\Model\AdminMenuGroupModel::where('is_on', 1)->find($id);

        if (!$data) {
            throw new ApiException('栏目不存在!');
        }

        return $data;
    }

    /**
     * 添加
     * @param array $data 要添加的数据
     * @return bool
     * @throws ApiException
     */
    public static function addAdminMenuGroup($data)
    {

        if (isset($data['parent_id']) && $data['parent_id'] > 0) {
            $data['level'] = 2;

            //验证父级
            $menu_group = \App\Model\AdminMenuGroupModel::where('is_on', 1)->find($data['parent_id']);
            if (!$menu_group || $menu_group->level != 1) {
                throw new ApiException('父级栏目不存在！');
            }

        } else {
            $data['level'] = 1;
        }

        $admin_menu_group_model = new \App\Model\AdminMenuGroupModel();
        set_save_data($admin_menu_group_model, $data);
        $res = $admin_menu_group_model->save();
        if (!$res) {
            throw new DatabaseException();
        }

        return true;
    }

    /**
     * 修改信息
     * @param array $data 修改的信息
     * @param int $id ID
     * @return bool
     * @throws ApiException
     */
    public static function updateAdminMenuGroup($data, $id)
    {
        //是否存在
        $res = \App\Model\AdminMenuGroupModel::where('is_on', 1)->find($id);

        if (!$res) {
            throw new ApiException('栏目不存在!');
        }

        set_save_data($res, $data);
        $update = $res->save();
        if (!$update) {
            throw new DatabaseException();
        }

        return true;
    }

    /**
     * 删除
     * @param int $id ID
     * @return bool
     * @throws ApiException
     */
    public static function deleteAdminMenuGroup($id)
    {
        $menu = \App\Model\AdminMenuGroupModel::where('is_on', 1)->find($id);

        if (!$menu) {
            throw new ApiException('栏目不存在');
        }

        //判断是否父栏目
        if ($menu->level == 1) {
            //判断是否还存在子栏目
            $child_menu = \App\Model\AdminMenuGroupModel::where('is_on', 1)
                ->where('parent_id', $menu->id)
                ->select('id')
                ->first();
            if ($child_menu) {
                throw new ApiException('请先删除子栏目！');
            }
        }

        //判断是否有相关菜单绑定到该栏目
        $bind_menu = \App\Model\AdminMenuModel::where('is_on', 1)
            ->where('menu_group_id', $id)
            ->first();
        if ($bind_menu) {
            throw new ApiException('还存在菜单关联，不能删除!');
        }

        set_save_data($menu, ['is_on' => 0]);
        $update = $menu->save();

        if (!$update) {
            throw new DatabaseException();
        }

        return true;
    }
}
