<?php

namespace App\Logic\Admin;

use Taoran\Laravel\Exception\ApiException;

class AdminPermissionLogic
{

    /**
     * 权限列表
     * @param int $data 用于判断的数据
     * @return array
     */
    public static function getAdminPermissionList($data)
    {
        $list = \App\Model\AdminPermissionModel::where('is_on', 1)->orderBy('sort');

        $get_one = array();

        if (isset($data['permission_id'])) {
            $get_one = \App\Model\AdminPermissionModel::where('is_on', 1)->find($data['permission_id']);

            if (!$get_one) {
                throw new ApiException('权限不存在!');
            }

            $list = $list->where('parent_id', $data['permission_id'])->paginate(15);
        } else {
            $list = $list->where('parent_id', 0)
                ->paginate(15);
        }

        return [
            'list' => $list,
            'data' => $get_one
        ];
    }

    /**
     * 单个权限数据
     * @param int $id 权限ID
     * @return \App\Model\AdminPermissionModel|array|\Illuminate\Database\Query\Builder|null|\stdClass
     * @throws ApiException
     */
    public static function getOneAdminPermission($id)
    {
        $data = \App\Model\AdminPermissionModel::where('is_on', '=', 1)->find($id);

        if (!$data) {
            throw new ApiException('权限不存在!');
        }
        return $data;
    }

    /**
     * 添加权限
     * @param array $data 添加的权限信息
     * @return bool
     * @throws ApiException
     */
    public static function addAdminPermission($data)
    {
        $admin_permission_model = new \App\Model\AdminPermissionModel();
        set_save_data($admin_permission_model, $data);
        $res = $admin_permission_model->save();
        if (empty($res)) {
            throw new ApiException('添加权限失败');
        }
        return true;
    }

    /**
     * 修改权限
     * @param array $data 修改的数据
     * @param int $id 权限ID
     * @return bool
     * @throws ApiException
     */
    public static function udpateAdminPermission($data, $id)
    {
        $res = \App\Model\AdminPermissionModel::where('is_on', '=', 1)->find($id);

        if (!$res) {
            throw new ApiException('权限不存在!');
        }

        set_save_data($res, $data);
        $update = $res->save($data);
        if (!$update) {
            throw new DatabaseException();
        }
        return true;
    }

    /**
     * 删除权限
     * @param int $id 权限ID
     * @return bool
     * @throws ApiException
     */
    public static function deleteAdminPermission($id)
    {
        $permission = \App\Model\AdminPermissionModel::where('is_on', '=', 1)
            ->select(['id', 'level'])
            ->find($id);

        if (!$permission) {
            throw new ApiException('权限不存在!');
        }

        if ($permission->level == 1) {
            $res = \App\Model\AdminPermissionModel::where('id', '=', $id)
                ->orWhere('parent_id', '=', $id)
                ->update(['is_on' => 0]);
        } else if ($permission->level == 2) {
            set_save_data($permission, [
                'is_on' => 0
            ]);

            $res = $permission->save();
        }

        if (!$res) {
            throw new DatabaseException();
        }
        return true;
    }

    /**
     * 给权限添加菜单
     * @param array $data 需要添加的数据
     * @param int $admin_permission_id 权限ID
     * @return array
     * @throws ApiException
     * @throws \Exception
     */
    public static function addAdminPermissionMenu($data, $admin_permission_id)
    {
        $list = [];
        if (empty($data)) {
            throw new ApiException('添加失败');
        }

        \DB::beginTransaction();
        foreach ($data as $val) {
            $menu_permission = \App\Model\AdminPermissionMenuModel::where('admin_permission_id', '=', $admin_permission_id)
                ->where('admin_menu_id', $val['admin_menu_id'])
                ->first();
            if ($menu_permission) {
                throw new ApiException('重复添加!');
            }

            $admin_permission_menu_model = new \App\Model\AdminPermissionMenuModel();
            $admin_permission_menu_data = array(
                'admin_permission_id' => $admin_permission_id,
                'admin_menu_id' => $val['admin_menu_id']
            );

            set_save_data($admin_permission_menu_model, $admin_permission_menu_data);
            $res = $admin_permission_menu_model->save();
            if (!$res) {
                \DB::rollBack();
                throw new ApiException('添加失败');
            }

            $list[] = $admin_permission_menu_model->id;
        }

        \DB::commit();

        return $list;
    }

    /**
     * 删除权限的菜单
     * @param array $data 要删除的菜单ID数组
     * @param int $admin_permission_id 菜单的ID
     * @return bool
     * @throws ApiException
     */
    public static function deleteAdminPermissionMenu($data, $admin_permission_id)
    {
        if (empty($data)) {
            throw new ApiException('删除失败');
        }

        foreach ($data as $val) {
            $res = \App\Model\AdminPermissionMenuModel::where('admin_permission_id', '=', $admin_permission_id)
                ->where('admin_menu_id', '=', $val['admin_menu_id'])
                ->delete();
        }
        if (!$res) {
            throw new ApiException('删除失败');
        }

        return true;
    }

    /**
     * 获取当前权限的菜单
     * @param int $admin_permission_id 权限ID
     * @return array
     */
    public static function getAdminPermissMenuionList($admin_permission_id)
    {
        $list = [];
        //查询所有的菜单
        $admin_menu_all = \App\Model\AdminMenuModel::where('is_on', '=', 1)->get();

        //查询当前权限的关联菜单
        $admin_permission_menu = \App\Model\AdminPermissionMenuModel::where('admin_permission_id', '=', $admin_permission_id)->get();

        $admin_menu_id = [];
        if (!$admin_permission_menu->isEmpty()) {
            //将查询角色权限关联表的数据中的ID存到$admin_menu_id的键中，admin_menu_id存到值
            foreach ($admin_permission_menu as $val) {
                $admin_menu_id[$val->id] = $val->admin_menu_id;
            }
        }

        $get_list = [];
        if (!$admin_menu_all->isEmpty()) {
            foreach ($admin_menu_all as $val) {
                if ($val->level == 1) {
                    $get_list[$val->id] = $val->toArray();
                } elseif ($val->level == 2) {
                    //判断这个菜单是否属于当前权限
                    if (in_array($val->id, $admin_menu_id)) {
                        //如果这个菜单属于当前权限，则将关联菜单表的ID存入该菜单数据中传出
                        $admin_permission_menu_id = array_search($val->id, $admin_menu_id);
                        $val->admin_permission_menu_id = $admin_permission_menu_id;
                        $val->is_opt = 1;
                    } else {
                        $val->is_opt = 0;
                    }
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
}
