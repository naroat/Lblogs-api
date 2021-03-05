<?php

namespace App\Logic\Admin;

use App\Exceptions\ApiException;
use App\Exceptions\DatabaseException;

class AdminRolePermissionLogic
{

    /**
     * 角色的权限列表
     * @param int $admin_role_id 角色ID
     * @return array
     */
    public static function getAdminRolePermissionList($admin_role_id)
    {
        $list = [];

        //查询所有的权限
        $admin_permission_all = \App\Model\AdminPermissionModel::where('is_on', '=', 1)
            ->select(['id', 'name', 'code', 'description', 'parent_id', 'level']);

        $admin_permission_all = $admin_permission_all->get();

        //查询当前角色的关联权限
        $admin_role_permission = \App\Model\AdminRolePermissionModel::where('admin_role_id', '=', $admin_role_id)
            ->select(['id', 'admin_permission_id'])
            ->get();

        $admin_permission_id = [];
        if (!$admin_role_permission->isEmpty()) {
            //将查询角色权限关联表的数据中的ID存到$admin_permission_id的键中，admin_permission_id存到值
            foreach ($admin_role_permission as $val) {
                $admin_permission_id[$val->id] = $val->admin_permission_id;
            }
        }

        $get_list = [];
        if (!$admin_permission_all->isEmpty()) {
            foreach ($admin_permission_all as $val) {
                if ($val->level == 1) {
                    $get_list[$val->id] = $val->toArray();
                } elseif ($val->level == 2) {
                    //判断这个权限是否属于当前角色
                    if (in_array($val->id, $admin_permission_id)) {
                        //如果这个权限属于当前角色，则将关联权限表的ID存入该权限数据中传出
                        $admin_role_permission_id = array_search($val->id, $admin_permission_id);
                        $val->admin_role_permission_id = $admin_role_permission_id;
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

    /**
     * 给角色添加权限
     * @param array $data 需要添加的数据
     * @return array
     * @throws ApiException
     */
    public static function addAdminRolePermission($data, $admin_role_id)
    {
        $list = [];
        if (empty($data)) {
            throw new ApiException('添加失败');
        }
        foreach ($data as $val) {
            $res = \App\Model\AdminRolePermissionModel::where('admin_role_id', '=', $admin_role_id)
                ->where('admin_permission_id', '=', $val['admin_permission_id'])
                ->first();

            if (!empty($res)) {
                continue;
            }

            $admin_role_permission_model = new \App\Model\AdminRolePermissionModel();
            $get_admin_role_permission = array(
                'admin_role_id' => $admin_role_id,
                'admin_permission_id' => $val['admin_permission_id']
            );
            set_save_data($admin_role_permission_model, $get_admin_role_permission);
            $res = $admin_role_permission_model->save();
            if (empty($res)) {
                throw new ApiException('添加失败');
            }
            $list[] = $admin_role_permission_model->id;
        }

        return $list;
    }

    /**
     * 删除角色的权限
     * @param array $data 要删除的权限ID数组
     * @param int $admin_role_id 角色的ID
     * @return bool
     * @throws ApiException
     * @throws \Exception
     */
    public static function deleteAdminRolePermission($data, $admin_role_id)
    {
        if (empty($data)) {
            throw new ApiException('删除失败');
        }

        \DB::beginTransaction();
        foreach ($data as $val) {

            $res = \App\Model\AdminRolePermissionModel::where('admin_role_id', '=', $admin_role_id)
                ->where('admin_permission_id', '=', $val['admin_permission_id'])
                ->first();

            if (!$res) {
                continue;
            }

            $delete = $res->delete();
            if (empty($delete)) {
                \DB::rollBack();
                throw new DatabaseException();
            }
        }

        \DB::commit();
        return true;
    }
}
