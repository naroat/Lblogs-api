<?php

namespace App\Logic\Admin;

use Taoran\Laravel\Exception\ApiException;
use Carbon\Carbon;

class AdminMenuAvailableLogic
{

    public static function getAdminMenuList()
    {
        $admin_id = session('admin_user.admin_id');

        $list = self::getRoleMenu($admin_id);

        return $list;
    }

    /**
     * 获取角色的菜单
     * @param $admin_id
     * @return \App\Model\AdminMenuGroupModel|\App\Model\AdminMenuGroupModel[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     * @throws ApiException
     */
    public static function getRoleMenu($admin_id)
    {
        $role = session('admin_user.role');

        // 获取当前用户的角色ID
        $role_ids = \App\Model\AdminUserRoleModel::where('admin_user_id', $admin_id)->get();
        if ($role_ids->isEmpty()) {
            throw new ApiException('当前用户没有分配到任何的系统角色,请联系管理员!');
        }

        $role_ids_arr = $role_ids->pluck('admin_role_id')->toArray();

        //菜单列表
        $menu_list = \App\Model\AdminMenuModel::where('is_on', 1)
            ->where('level', 1)
            ->orderBy('order','asc')
            ->orderBy('id','asc');

        if (!in_array(1, $role)) {
            $menu_list->where('is_auth', 0);
        }

        //栏目列表
        $group_list = \App\Model\AdminMenuGroupModel::where('is_on', 1)
            ->where('level', 1)
            ->with(['childs' => function ($query) {
                $query->where('is_on', 1)
                    ->select('id', 'level', 'name', 'parent_id', 'url')
                    ->orderBy('order', 'asc');
            }])
            ->orderBy('order', 'asc')
            ->get();

        //超级管理员,全部权限
        if (in_array(1, $role_ids_arr)) {
            // 获取一级菜单
            $menu_list = $menu_list->with(['childs' => function ($query) {
                $query->where('is_on', 1)
                    ->orderBy('order','asc')
                    ->orderBy('id','asc')
                    ->select('id', 'level', 'name', 'parent_id', 'icon', 'url');
            }])->get();

        } else {
            // 根据用户的角色获取当前用户的权限
            $permission_ids = \App\Model\AdminRolePermissionModel::whereIn('admin_role_id', $role_ids_arr)
                ->select(['admin_permission_id'])
                ->get();

            $permission_ids_arr = $permission_ids->pluck('admin_permission_id')->unique()->toArray();

            // 菜单权限
            // 根据当前用户的权限获取当前用户可用的菜单
            $admin_menu_ids = \App\Model\AdminPermissionMenuModel::whereIn('admin_permission_id', $permission_ids_arr)
                ->select(['admin_menu_id'])
                ->get();

            $menu_list = $menu_list
                ->with(['childs' => function ($query) use ($admin_menu_ids) {
                    $query->where('is_on', 1)->whereIn('id', $admin_menu_ids)
                        ->orderBy('order','asc')
                        ->orderBy('id','asc')
                        ->select('id', 'name', 'parent_id', 'url', 'order');
                }])
                ->whereHas('childs', function ($query) use ($admin_menu_ids) {
                    $query->where('is_on', 1)->whereIn('id', $admin_menu_ids);
                })
                ->get();

            // 菜单的栏目id
            $menu_group_ids = $menu_list->pluck('menu_group_id')->unique()->toArray();

            // 查询栏目权限
            // 根据当前用户的权限获取当前用户可用的栏目
            $admin_group_ids = \App\Model\AdminPermissionMenuGroupModel::whereIn('admin_permission_id', $permission_ids_arr)
                ->select(['admin_menu_group_id'])
                ->get();

            $admin_group_ids = $admin_group_ids->pluck('admin_menu_group_id')->unique()->toArray();

            //合并
            $group_ids = array_unique(array_merge($menu_group_ids, $admin_group_ids));

            //过滤
            $new_group_list = collect();
            $group_list->each(function ($item) use ($group_ids, &$new_group_list) {
                $data = array(
                    'id' => $item->id,
                    'name' => $item->name,
                    'url' => $item->url,
                    'level' => $item->level,
                    'parent_id' => $item->parent_id,
                    'order' => $item->order,
                    'childs' => []
                );

                $childs = collect();
                if (!$item->childs->isEmpty()) {
                    $item->childs->each(function ($item) use ($group_ids, &$childs) {
                        if (in_array($item->id, $group_ids)) {
                            $childs->push($item);
                        }
                    });
                }

                $data['childs'] = $childs;

                if (in_array($item->id, $group_ids) || $item->childs) {
                    $new_group_list->push($data);
                }
            });
            $group_list = $new_group_list;
        }

        $new_group_list = collect();
        $group_list->each(function ($item) use ($menu_list, &$new_group_list) {
            if ($menu_list->where('menu_group_id', $item['id'])->count() > 0) {
                $item['second_menus'] = $menu_list->where('menu_group_id', $item['id'])->sortBy('order')->values();
            } else {
                $item['second_menus'] = [];
            }

            //子级
            $item['childs']->each(function ($item) use ($menu_list) {
                if ($menu_list->where('menu_group_id', $item['id'])->count() > 0) {
                    $item['second_menus'] = $menu_list->where('menu_group_id', $item['id'])->sortBy('order')->values();
                } else {
                    $item['second_menus'] = [];
                }
            });

            $new_group_list->push($item);
        });

        return $new_group_list;
    }


}
