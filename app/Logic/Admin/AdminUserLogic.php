<?php
namespace App\Logic\Admin;

use Taoran\Laravel\Exception\ApiException;
use App\Exceptions\DatabaseException;

class AdminUserLogic
{
    /**
     * 管理员列表
     *
     * @param $data
     * @return array
     */
    public static function getAdminUserList($data)
    {
        $list = \App\Model\AdminUserModel::where('is_on', '=', 1)->orderBy('id', 'DESC');

        //筛选名称
        if (isset($data['name'])) {
            $list->where('name', 'like', '%' . $data['name'] . '%');
        }

        //筛选名称
        if (isset($data['phone'])) {
            $list->where('phone', $data['phone']);
        }

        //筛选创建时间
        if (isset($data['start_time']) && isset($data['end_time'])) {
            if ($data['start_time'] > $data['end_time']) {
                throw new ApiException('开始时间不能大于结束时间');
            }

            $list->whereBetween('created_at', [$data['start_time'], $data['end_time']]);
        }

        $list = $list->paginate();

        $admin_id = session('admin_info.admin_id');

        //重装数据
        $list->each(function ($item) use ($admin_id) {
            //转换ip
            $item->last_login_ip = long2ip($item->last_login_ip);
        });

        $return_data = [
            'admin_user' => $list,
            'roles' => ['admin']
        ];

        return $return_data;
    }

    /**
     * 管理员单条数据
     * @param int $id 管理员ID
     * @return \App\Model\AdminUserModel|array|\Illuminate\Database\Query\Builder|null|\stdClass
     * @throws ApiException
     */
    public static function getOneAdminUser($id)
    {
        $admin_id = session('admin_user')['admin_id'];

        $data = \App\Model\AdminUserModel::where('is_on', '=', 1)->find($id);

        if (!$data) {
            throw new ApiException('不存在管理员用户');
        }

        //转换ip
        $data->last_login_ip = long2ip($data->last_login_ip);

        //是否当前管理员
        $data->self = 0;
        if ($admin_id == $data->id) {
            $data->self = 1;
        }

        return $data;
    }

    /**
     * 添加管理员
     * @param array $data 要添加的数据
     * @return bool
     * @throws ApiException
     * @throws \Exception
     */
    public static function addAdminUser($data)
    {
        //验证用户名是否已经被使用
        $verift_admin = \App\Model\AdminUserModel::where('is_on', 1)
            ->where('account', $data['account'])
            ->first(['id']);

        if (!empty($verift_admin)) {
            throw new ApiException('该用户已被注册');
        }

        $salt = '';
        $get_password = create_password($data['password'], $salt);

        \DB::beginTransaction();

        $admin_user_model = new \App\Model\AdminUserModel();
        $get_admin_data = array(
            'account' => $data['account'],
            'phone' => isset($data['phone']) ? $data['phone'] : 0,
            'password' => $get_password,
            'salt' => $salt,
            'name' => $data['name'],
            'last_login_ip' => ip2long(Request()->getClientIp())
        );

        //上传的头像
        /*if (isset($data['headimg'])) {
            if (!empty($data['headimg'])) {
                $path = \App\Model\UploadModel::where('is_on', 1)
                    ->select('path')
                    ->find($data['headimg']);

                if (!$path) {
                    \DB::beginTransaction();
                    throw new ApiException('图片不存在!');
                }

                $get_admin_data['headimg'] = $path->path;
            } else {
                $get_admin_data['headimg'] = '';
            }

        }*/

        set_save_data($admin_user_model, $get_admin_data);
        $res = $admin_user_model->save();

        if (!$res) {
            \DB::rollback();
            throw new ApiException();
        }

        foreach ($data['role_ids'] as $val) {

            //判断用户角色是否存在
            $is_role = \App\Model\AdminRoleModel::where('is_on', 1)->find($val['id']);

            if (!$is_role) {
                \DB::beginTransaction();
                throw new \App\Exceptions\ApiException('角色不存在!');
            }

            $admin_user_role_model = new \App\Model\AdminUserRoleModel();
            $get_admin_user_role_data = [
                'admin_user_id' => $admin_user_model->id,
                'admin_role_id' => $val['id']
            ];
            set_save_data($admin_user_role_model, $get_admin_user_role_data);
            $res_tow = $admin_user_role_model->save();
            if (empty($res_tow)) {
                \DB::rollBack();
                throw new ApiException();
            }
        }

        \DB::commit();
        return true;
    }

    /**
     * 修改管理员信息
     * @param array $data 需要修改的数据
     * @param int $id 管理员ID
     * @throws ApiException
     * @throws \Exception
     */
    public static function updateAdminUser($data, $id)
    {
        $res = \App\Model\AdminUserModel::where('is_on', 1)->find($id);

        if (!$res) {
            throw new ApiException('管理员不存在!');
        }

        $admin_id = session('admin_info.admin_id');

        //修改密码
        if (isset($data['password'])) {
            if ($id == 1) {
                throw new ApiException('你没有权限修改系统管理员密码!');
            }

            $get_password = create_password($data['password'], $salt);
            $data['password'] = $get_password;
            $data['salt'] = $salt;
        }

        //改头像
        /*if (isset($data['headimg'])) {
            if (!empty($data['headimg'])) {
                $path = \App\Model\UploadModel::where('is_on', 1)
                    ->select('path')
                    ->find($data['headimg']);

                if (!$path) {
                    throw new ApiException('图片不存在!');
                }

                $data['headimg'] = $path->path;
            }
        }*/

        //判断手机
        if (isset($data['phone']) && $data['phone'] == '') {
            $data['phone'] = 0;
        }

        \DB::beginTransaction();

        if (isset($data['role_ids'])) {
            if ($id == 1) {
                throw new ApiException('默认系统超级管理员不能被改动角色!');
            }

            if ($admin_id == $id) {
                throw new ApiException('不可以修改自己的角色!');
            }

            foreach ($data['role_ids'] as $v) {
                //判断是否存在
                $is_role = \App\Model\AdminRoleModel::where('is_on', 1)
                    ->select('id')
                    ->find($v['id']);

                if (!$is_role) {
                    \DB::rollBack();
                    throw new ApiException('管理员角色不存在!');
                }
            }

            //先删除之前该用户所有角色
            $is_delete = \App\Model\AdminUserRoleModel::where('admin_user_id', $id)
                ->delete();

            if (!$is_delete) {
                \DB::rollBack();
                throw new DatabaseException();
            }

            foreach ($data['role_ids'] as $v) {
                $admin_user_role_model = new \App\Model\AdminUserRoleModel();
                set_save_data($admin_user_role_model, [
                    'admin_user_id' => $id,
                    'admin_role_id' => $v['id']
                ]);
                $save = $admin_user_role_model->save();
                if (!$save) {
                    \DB::rollBack();
                    throw new ApiException();
                }
            }

            unset($data['role_ids']);
        }

        set_save_data($res, $data);
        $update = $res->save();

        if (!$update) {
            \DB::rollBack();
            throw new ApiException();
        }
        \DB::commit();

        return true;
    }

    /**
     * 删除管理员
     * @param int $id 管理员ID
     * @throws ApiException
     * @throws \Exception
     */
    public static function deleteAdminUser($id)
    {
        if ($id == 1) {
            throw new ApiException('系统管理员不能被删除!');
        }

        $res = \App\Model\AdminUserModel::where('is_on', 1)->find($id);

        if (!$res) {
            throw new ApiException('管理员不存在!');
        }

        \DB::beginTransaction();

        set_save_data($res, [
            'is_on' => 0
        ]);
        $update = $res->save();
        if (!$update) {
            \DB::rollBack();
            throw new ApiException();
        }

        //删除管理角色
        $delete_role = \App\Model\AdminUserRoleModel::where('admin_user_id', $id)
            ->delete();
        if (!$delete_role) {
            \DB::rollBack();
            throw new ApiException();
        }

        \DB::commit();

        return true;
    }

    /**
     * 判断当前登录状态
     * @return array
     */
    public static function status()
    {
        $data = [
            'is_user' => 0
        ];

        if (!empty(session('admin_info.admin_id'))) {
            $data['is_user'] = 1;
        }

        return $data;
    }
}
