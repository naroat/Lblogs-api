<?php
namespace App\Repositorys;

use App\Model\AdminUserRoleModel;
use Taoran\Laravel\Exception\ApiException;
use Taoran\Laravel\Repository;

class AdminUserRoleRepository extends Repository
{
    protected $model;

    public function __construct()
    {
        $this->model = new AdminUserRoleModel();
    }

    /**
     * 通过管理员id删除对应角色关联
     *
     * @param $id
     */
    public function deleteByAdminUserId($id)
    {
        $is_delete = $this->model->where('admin_user_id', $id)->delete();

        if (!$is_delete) {
            \DB::rollback();
            throw new ApiException();
        }
    }
}
