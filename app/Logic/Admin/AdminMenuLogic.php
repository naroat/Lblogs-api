<?php

namespace App\Logic\Admin;

use Taoran\Laravel\Exception\ApiException;
use Illuminate\Support\Facades\Redis;

class AdminMenuLogic
{

    /**
     * 管理员菜单列表
     * @return array
     */
    public static function getAdminMenuList($data)
    {
        $role = session('admin_user.role');

        $list = \App\Model\AdminMenuModel::where('is_on', '=', 1)->orderBy('order');

        if (!in_array(1, $role)) {
            $list->where('is_auth', 0);
        }

        $res = array();

        if (isset($data['menu_id'])) {
            $res = \App\Model\AdminMenuModel::where('is_on', '=', 1)->find($data['menu_id']);

            $list = $list->where('parent_id', '=', $data['menu_id'])->paginate(15);
        } else {
            $list = $list->where('parent_id', '=', 0)
                ->with(['childs' => function ($query) {
                    $query->where('is_on', 1)->select(['id', 'parent_id']);
                }])
                ->with(['menuGroup' => function ($query) {
                    $query->with(['parent' => function ($query) {
                            $query->select('id', 'name', 'level', 'parent_id');
                        }]);
                }])
                ->paginate(15);

            $list->each(function ($item) {
                if ($item->childs->isEmpty()) {
                    $item->is_childs = 0;
                } else {
                    $item->is_childs = 1;
                }
                unset($item->childs);

                $menu_group_str = '';
                if ($item->menuGroup) {
                    $menu_group_arr = [];

                    if ($item->menuGroup->parent) {
                        $menu_group_arr = [$item->menuGroup->parent->name];
                    }

                    $menu_group_arr[] = $item->menuGroup->name;;

                    $menu_group_str = implode('-', $menu_group_arr);
                }

                $item->menu_group_str = $menu_group_str;
                unset($item->menuGroup);
            });
        }

        return [
            'data' => $res,
            'list' => $list
        ];
    }

    /**
     * 获取一条管理员菜单数据
     * @param int $id 菜单ID
     * @return \App\Model\AdminMenuModel|array|\Illuminate\Database\Query\Builder|null|\stdClass
     * @throws ApiException
     */
    public static function getOneAdminMenu($id)
    {
        $data = \App\Model\AdminMenuModel::where('is_on', '=', 1)->find($id);

        if (!$data) {
            throw new ApiException('菜单不存在!');
        }

        return $data;
    }

    /**
     * 添加管理员菜单
     * @param array $data 要添加的数据
     * @return bool
     * @throws ApiException
     */
    public static function addAdminMenu($data)
    {
        $admin_menu_model = new \App\Model\AdminMenuModel();
        set_save_data($admin_menu_model, $data);
        $res = $admin_menu_model->save();
        if (!$res) {
            throw new DatabaseException();
        }
        return true;
    }

    /**
     * 修改管理员菜单
     * @param array $data 要修改的数据
     * @param int $id 菜单ID
     * @return bool
     * @throws ApiException
     */
    public static function updateAdminMenu($data, $id)
    {
        $menu = \App\Model\AdminMenuModel::where('is_on', '=', 1)->find($id);

        if (!$menu) {
            throw new ApiException('菜单不存在!');
        }

        if ($menu->level == 1 && isset($data['menu_group_id'])) {
            $menu_group = \App\Model\AdminMenuGroupModel::where('is_on', 1)
                ->select('id', 'level')
                ->find($data['menu_group_id']);
            if (!$menu_group) {
                throw new ApiException('栏目不存在!');
            }

            $menu_group_child = \App\Model\AdminMenuGroupModel::where('is_on', 1)
                ->where('parent_id', $data['menu_group_id'])
                ->select('id', 'level')
                ->first();
            if ($menu_group_child) {
                throw new ApiException('栏目存在二级绑定,不能被绑定菜单!');
            }
        }

        set_save_data($menu, $data);
        $update = $menu->save();
        if (!$update) {
            throw new DatabaseException();
        }

        return true;
    }

    /**
     * 删除管理员菜单
     * @param int $id 菜单ID
     * @return bool
     * @throws ApiException
     */
    public static function deleteAdminMenu($id)
    {
        $res = \App\Model\AdminMenuModel::where('is_on', '=', 1)
            ->select(['id', 'level'])
            ->find($id);

        if (!$res) {
            throw new ApiException('菜单不存在!');
        }

        \DB::beginTransaction();
        if (!empty($res->level) && $res->level == 1) {
            //查询子级
            $child_menu = \App\Model\AdminMenuModel::where('parent_id', '=', $id)
                ->where('is_on', 1)
                ->get(['id']);

            $child_arr = $child_menu->pluck('id')->toArray();

            if ($child_arr) {

                \DB::rollBack();
                throw new ApiException('请删除子菜单后再操作!');
            }

            //删除权限菜单表中的
            $permission_menu = \App\Model\AdminPermissionMenuModel::whereIn('admin_menu_id', $child_arr)
                ->first(['id']);

            if ($permission_menu) {
                $delete_permission_menu = \App\Model\AdminPermissionMenuModel::whereIn('admin_menu_id', $child_arr)->delete();
                if (!$delete_permission_menu) {
                    \DB::rollBack();
                    throw new DatabaseException();
                }
            }
        }

        set_save_data($res, ['is_on' => 0]);
        $update = $res->save();

        if (!$update) {
            \DB::rollBack();
            throw new DatabaseException();
        }

        \DB::commit();
        return true;
    }

    public static function getMenuList()
    {
        $list = \App\Model\AdminMenuModel::where('is_on', 1)
            ->where('url', '!=', '');

        $list = $list->get();

        return $list;
    }
}
