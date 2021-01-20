<?php


namespace App\Services;


use App\Repositorys\AdminRoleRepository;
use App\Repositorys\AdminUserRepository;
use App\Repositorys\AdminUserRoleRepository;
use Taoran\Laravel\Exception\ApiException;

class AdminUserService
{
    protected $adminUserResponse;
    protected $adminRoleResponse;
    protected $adminUserRoleResponse;

    public function __construct()
    {
        $this->adminUserResponse = new AdminUserRepository();
        $this->adminRoleResponse = new AdminRoleRepository();
        $this->adminUserRoleResponse = new AdminUserRoleRepository();
    }

    /**
     * 获取管理员列表
     *
     * @param $param
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     *
     */
    public function getAdminUserList($param)
    {
        $list = $this->adminUserResponse->getAdminUserList($param);

        //重装数据
        $list->each(function ($item) {
            $item->role_string = $item->roles->implode('name', ',');
            unset($item->roles);

            //转换ip
            $item->last_login_ip = long2ip($item->last_login_ip);
        });

        return $list;
    }

    /**
     * 添加管理员
     *
     * @return bool
     */
    public function addAdminUser($param)
    {
        //验证用户名是否已经被使用
        $admin_user = $this->adminUserResponse->getAdminUserOne($param);
        if ($admin_user) {
            throw new ApiException('该用户已被注册');
        }

        \DB::beginTransaction();

        $password = create_password($param['password'], $salt);

        $admin_user_model = $this->adminUserResponse->create([
            'account' => $param['account'],
            'phone' => isset($param['phone']) ? $param['phone'] : 0,
            'password' => $password,
            'salt' => $salt,
            'name' => $param['name'],
            'last_login_ip' => ip2long(Request()->getClientIp())
        ]);


        foreach ($param['role_ids'] as $val) {

            //判断用户角色是否存在
            $is_role = $this->adminRoleResponse->getOneById($val['id']);

            if (!$is_role) {
                \DB::rollback();
                throw new ApiException('角色不存在!');
            }

            $this->adminUserRoleResponse->create([
                'admin_user_id' => $admin_user_model->id,
                'admin_role_id' => $val['id']
            ]);
        }

        \DB::commit();
        return true;
    }

    /**
     * 获取管理员详细
     *
     * @param $id
     * @throws ApiException
     */
    public function getAdminUserOne($id)
    {
        $admin_user = $this->adminUserResponse->getOneById($id, function ($query) {
            $query->with(['roles' => function ($query) {
                $query->select('name');
            }]);
        });
        if (!$admin_user) {
            throw new ApiException('管理员不存在!');
        }

        $admin_user->role_string = $admin_user->roles->implode('name', ',');
        unset($admin_user->roles);

        //转换ip
        $admin_user->last_login_ip = long2ip($admin_user->last_login_ip);

        return $admin_user;
    }

    /**
     * 更新用户
     *
     * @param $param
     * @param $id
     * @return bool
     * @throws ApiException
     */
    public function updateAdminUser($id, $param)
    {
        $admin_id = 1;  //TODO

        $admin_user = $this->adminUserResponse->getOneById($id);
        if (!$admin_user) {
            throw new ApiException('管理员不存在!');
        }

        //修改密码
        if (isset($param)) {
            $param['password'] = create_password($param['password'], $salt);
            $param['salt'] = $salt;
        }

        if (isset($param['role_ids'])) {
            if ($id == 1) {
                throw new ApiException('默认系统超级管理员不能被改动角色!');
            }

            if ($admin_id == $id) {
                throw new ApiException('不可以修改自己的角色!');
            }

            foreach ($param['role_ids'] as $v) {
                //判断是否存在
                $is_role = $this->adminRoleResponse->getOneById($v['id']);

                if (!$is_role) {
                    \DB::rollBack();
                    throw new ApiException('管理员角色不存在!');
                }
            }

            //先删除之前该用户所有角色
            $this->adminUserRoleResponse->deleteByAdminUserId($id);

            foreach ($param['role_ids'] as $v) {
                //添加角色关联
                $this->adminUserRoleResponse->create([
                    'admin_user_id' => $id,
                    'admin_role_id' => $v['id']
                ]);
            }

            unset($param['role_ids']);
        }

        $this->adminUserResponse->update($admin_user, $param);

        return true;
    }

    /**
     * 删除管理员
     *
     * @param $id
     * @return bool
     * @throws ApiException
     */
    public function deleteAdminUser($id)
    {
        if ($id == 1) {
            throw new ApiException('系统管理员不能被删除!');
        }

        $admin_user = $this->adminUserResponse->getOneById($id);
        if (!$admin_user) {
            throw new ApiException('管理员不存在!');
        }

        \DB::beginTransaction();

        $res = $this->adminUserResponse->update($admin_user, [
            'is_on' => 0
        ]);

        if (!$res) {
            DB::rollback();
            throw new ApiException();
        }

        //删除管理角色
        $this->adminUserRoleResponse->deleteByAdminUserId($id);

        \DB::commit();

        return true;
    }
}
