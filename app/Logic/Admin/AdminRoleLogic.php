<?php

namespace App\Logic\Admin;

use Taoran\Laravel\Exception\ApiException;

class AdminRoleLogic
{

    /**
     * 角色列表
     * @param $data array 筛选数组
     *        $data.is_page 是否分页
     * @return \App\Model\AdminRoleModel|\Illuminate\Database\Query\Builder
     */
    public static function getAdminRoleList($data)
    {
        $list = \App\Model\AdminRoleModel::where('is_on', '=', 1)
            ->withCount(['admins' => function ($query) {
                $query->where('is_on', 1);
            }]);

        if (isset($data['is_page']) && $data['is_page'] == 0) {
            $list = $list->get();
        } else {
            $list = $list->paginate();
        }

        $list->each(function ($item) {
            if ($item->id == 1) {
                $item->is_manage = 0;
                $item->is_update = 0;
                $item->is_delete = 0;
            } else {
                $item->is_manage = 1;
                $item->is_update = 1;
                $item->is_delete = 1;
            }
        });

        return $list;
    }

    /**
     * 角色单个数据
     * @param int $id 角色ID
     * @return \App\Model\AdminRoleModel|array|\Illuminate\Database\Query\Builder|null|\stdClass
     * @throws ApiException
     */
    public static function getOneAdminRole($id)
    {
        $data = \App\Model\AdminRoleModel::where('is_on', '=', 1)->find($id);

        if (!$data) {
            throw new ApiException('角色不存在!');
        }
        return $data;
    }

    /**
     * 添加角色
     * @param array $data 添加的角色信息
     * @return bool
     */
    public static function addAdminRole($data)
    {
        $admin_role_model = new \App\Model\AdminRoleModel();
        set_save_data($admin_role_model, $data);
        $admin_role_model->save();

        return true;
    }

    /**
     * 修改角色
     * @param array $data 修改的角色信息
     * @param int $id 角色ID
     * @return bool
     * @throws ApiException
     */
    public static function updateAdminRole($data, $id)
    {
        if (empty($data)) {
            throw new ApiException('你没有做任何修改');
        }

        $res = \App\Model\AdminRoleModel::where('is_on', 1)->find($id);

        if (!$res) {
            throw new ApiException('用户角色不存在!');
        }

        set_save_data($res, $data);
        $update = $res->save();
        if (!$update) {
            throw new DatabaseException();
        }
        return true;
    }

    /**
     * 删除角色
     * @param int $id 角色ID
     * @return bool
     * @throws ApiException
     */
    public static function deleteAdminRole($id)
    {
        $res = \App\Model\AdminRoleModel::where('is_on', 1)->find($id);

        if (!$res) {
            throw new ApiException('用户角色不存在!');
        }

        $is_admin = $res->admins()->where('admin_user.is_on', 1)->first();
        if (!empty($is_admin)) {
            throw new ApiException('角色下还存在管理员,角色不能被删除!');
        }

        set_save_data($res, [
            'is_on' => 0
        ]);

        $delete = $res->save();
        if (!$delete) {
            throw new DatabaseException();
        }

        return true;
    }

}
