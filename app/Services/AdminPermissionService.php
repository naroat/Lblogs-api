<?php

namespace App\Services;

use App\Exceptions\DatabaseException;
use App\Repositorys\AdminMenuRepository;
use App\Repositorys\AdminPermissionRepository;
use Taoran\Laravel\Exception\ApiException;
use App\Repositorys\AdminPermissionMenuRepository;

class AdminPermissionService
{
    protected $adminPermissionRepository;
    protected $adminMenuRepository;
    protected $adminPermissionMenuRepository;

    public function __construct()
    {
        $this->adminPermissionRepository = new AdminPermissionRepository();
        $this->adminMenuRepository = new AdminMenuRepository();
        $this->adminPermissionMenuRepository = new AdminPermissionMenuRepository();
    }

    /**
     * 权限列表
     * @param int $data 用于判断的数据
     * @return array
     */
    public function getAdminPermissionList($data)
    {
        $role = session('admin_info.role');

        $get_one = array();

        $list = $this->adminPermissionRepository->getList($data, function ($query) use ($role, $data, &$get_one) {
            $query->where('is_on', 1)->orderBy('sort');
            if (!in_array(1, $role)) {
                $query->where('is_auth', 0);
            }
            if (isset($data['permission_id'])) {
                $get_one = \App\Model\AdminPermissionModel::where('is_on', '=', 1)
                    ->select('id', 'name')
                    ->find($data['permission_id']);

                if (!$get_one) {
                    throw new ApiException('权限不存在!');
                }
                $query->where('parent_id', '=', $data['permission_id'])->paginate(15);
            } else {
                $query->where('parent_id', '=', 0)->paginate(15);
            }
        });

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
    public function getOneAdminPermission($id)
    {
        $data = $this->adminPermissionRepository->getOne(function ($query) use ($id) {
            $query->where('id', $id);
        });

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
    public function addAdminPermission($data)
    {
        $this->adminPermissionRepository->create($data);
        return true;
    }

    /**
     * 修改权限
     * @param array $data 修改的数据
     * @param int $id 权限ID
     * @return bool
     * @throws ApiException
     */
    public function udpateAdminPermission($data, $id)
    {
        $res = $this->adminPermissionRepository->getOneById($id);

        if (!$res) {
            throw new ApiException('权限不存在!');
        }

        $this->adminPermissionRepository->update($res, $data);

        return true;
    }

    /**
     * 删除权限
     * @param int $id 权限ID
     * @return bool
     * @throws ApiException
     */
    public function deleteAdminPermission($id)
    {
        $permission = $this->adminPermissionRepository->getOneById($id);

        if (!$permission) {
            throw new ApiException('权限不存在!');
        }

        if ($permission->level == 1) {
            $res = $this->adminPermissionRepository->getOne(function ($query) use ($id) {
                $query->where('id', '=', $id)->orWhere('parent_id', '=', $id);
            });

            $this->adminPermissionRepository->update($res, [
                'is_on' => 0
            ]);
        } else if ($permission->level == 2) {
            $this->adminPermissionRepository->update($permission, [
                'is_on' => 0
            ]);
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
    public function addAdminPermissionMenu($data, $admin_permission_id)
    {
        $list = [];
        if (empty($data)) {
            throw new ApiException('添加失败');
        }

        \DB::beginTransaction();
        foreach ($data as $val) {
            $menu_permission = $this->adminPermissionMenuRepository->getOne(function ($query) use ($admin_permission_id, $val) {
                $query->where('admin_permission_id', '=', $admin_permission_id)
                    ->where('admin_menu_id', $val['admin_menu_id']);
            });
            if ($menu_permission) {
                throw new ApiException('重复添加!');
            }

            $admin_permission_menu_data = array(
                'admin_permission_id' => $admin_permission_id,
                'admin_menu_id' => $val['admin_menu_id']
            );
            $admin_permission_menu_model = $this->adminPermissionMenuRepository->create($admin_permission_menu_data);

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
    public function deleteAdminPermissionMenu($data, $admin_permission_id)
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
    public function getAdminPermissMenuionList($admin_permission_id)
    {
        $list = [];
        //查询所有的菜单
        $admin_menu_all = $this->adminMenuRepository->getList([
            'is_all' => 1
        ]);

        //查询当前权限的关联菜单
        $admin_permission_menu = $this->adminPermissionMenuRepository->getList([
            'is_all' => 1
        ], function ($query) use ($admin_permission_id) {
            $query->where('admin_permission_id', $admin_permission_id);
        });

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
