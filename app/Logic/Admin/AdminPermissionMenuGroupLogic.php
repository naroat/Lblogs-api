<?php

namespace App\Logic\Admin;

use Taoran\Laravel\Exception\ApiException;

class AdminPermissionMenuGroupLogic
{

    /**
     * 获取当前权限的菜单
     * @param int $admin_permission_id 权限ID
     * @return array
     */
    public static function getAdminPermissMenuionGroupList($admin_permission_id)
    {
        $list = [];
        //查询所有的菜单
        $admin_menu_all = \App\Model\AdminMenuGroupModel::where('is_on', 1)
            ->select(['id', 'name', 'parent_id', 'level'])
            ->get();

        //查询当前权限的关联菜单
        $admin_permission_menu = \App\Model\AdminPermissionMenuGroupModel::where('admin_permission_id', '=', $admin_permission_id)
            ->select(['id', 'admin_menu_group_id'])
            ->get();

        $admin_menu_id = [];
        if (!$admin_permission_menu->isEmpty()) {
            //将查询角色权限关联表的数据中的ID存到$admin_menu_id的键中，admin_menu_id存到值
            foreach ($admin_permission_menu as $val) {
                $admin_menu_id[$val->id] = $val->admin_menu_group_id;
            }
        }

        $get_list = [];
        if (!$admin_menu_all->isEmpty()) {
            foreach ($admin_menu_all as $val) {
                //判断这个菜单是否属于当前权限
                if (in_array($val->id, $admin_menu_id)) {
                    //如果这个菜单属于当前权限，则将关联菜单表的ID存入该菜单数据中传出
                    $admin_permission_menu_id = array_search($val->id, $admin_menu_id);
                    $val->admin_permission_menu_id = $admin_permission_menu_id;
                    $val->is_opt = 1;
                } else {
                    $val->is_opt = 0;
                }

                if ($val->level == 1) {
                    $get_list[$val->id] = $val->toArray();
                } elseif ($val->level == 2) {
                    $get_list[$val->parent_id]['child'][] = $val->toArray();
                }
            }
        }

        if (!empty($get_list)) {
            foreach ($get_list as $val) {
                $list[] = $val;
            }
        }

        return $list;
    }

    /**
     * 添加
     * @param array $data 要添加的数据
     * @return bool
     * @throws ApiException
     */
    public static function addAdminPermissionMenuGroup($data, $permission_id)
    {
        $menu_permission = \App\Model\AdminPermissionMenuGroupModel::where('admin_permission_id', $permission_id)
            ->where('admin_menu_group_id', $data['admin_menu_group_id'])
            ->first();
        if ($menu_permission) {
            throw new ApiException('重复添加!');
        }

        $admin_permission_menu_group_model = new \App\Model\AdminPermissionMenuGroupModel();
        $admin_permission_menu_data = array(
            'admin_permission_id' => $permission_id,
            'admin_menu_group_id' => $data['admin_menu_group_id']
        );

        set_save_data($admin_permission_menu_group_model, $admin_permission_menu_data);
        $res = $admin_permission_menu_group_model->save();
        if (!$res) {
            \DB::rollBack();
            throw new ApiException('添加失败');
        }

        return true;
    }

    /**
     * 删除
     * @param int $id ID
     * @return bool
     * @throws ApiException
     */
    public static function deleteAdminPermissionMenuGroup($data, $permission_id)
    {
        $menu_permission = \App\Model\AdminPermissionMenuGroupModel::where('admin_permission_id', $permission_id)
            ->where('admin_menu_group_id', $data['admin_menu_group_id'])
            ->first();

        if (!$menu_permission) {
            throw new ApiException('数据不存在');
        }

        $update = $menu_permission->delete();
        if (!$update) {
            throw new ApiException();
        }

        return true;
    }
}
