<?php


namespace App\Repositorys;


use App\Model\AdminRoleModel;
use Taoran\Laravel\Exception\ApiException;
use Taoran\Laravel\Repository;

class AdminRoleRepository extends Repository
{
    protected $model;

    public function __construct()
    {
        $this->model = new AdminRoleModel();
    }

    /**
     * 删除角色
     *
     * @param $id
     * @return bool
     * @throws ApiException
     */
    public function deleteAdminRole($id)
    {
        $res = $this->getOneById($id);
        if (!$res) {
            throw new ApiException('用户角色不存在!');
        }

        $is_admin = $res->admins()->where('admin_user.is_on',1)->first();
        if (!empty($is_admin)) {
            throw new ApiException('角色下还存在管理员,角色不能被删除!');
        }

        $this->update($res, [
            'is_on' => 0
        ]);

        return true;
    }
}
