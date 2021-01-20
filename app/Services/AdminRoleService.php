<?php


namespace App\Services;

use App\Repositorys\AdminRoleRepository;
use Taoran\Laravel\Exception\ApiException;

class AdminRoleService
{
    protected $adminRoleRepository;

    public function __construct()
    {
        $this->adminRoleRepository = new AdminRoleRepository();
    }

    /**
     * 获取角色列表
     */
    public function getList()
    {
        return $this->adminRoleRepository->getList([]);
    }

    /**
     * 添加角色
     *
     * @param $param
     * @return mixed
     * @throws \Taoran\Laravel\Exception\ApiException
     */
    public function addAdminRole($param)
    {
        return $this->adminRoleRepository->create($param);
    }

    /**
     * 角色信息
     *
     * @param int $id 角色ID
     * @return \App\Model\AdminRoleModel|array|\Illuminate\Database\Query\Builder|null|\stdClass
     * @throws ApiException
     */
    public function getOneAdminRole($id)
    {
        $data = $this->adminRoleRepository->getOneById($id);
        if (!$data) {
            throw new ApiException('角色信息不存在!');
        }
        return $data;
    }

    /**
     * 修改角色
     * @param array $data 修改的角色信息
     * @param int $id 角色ID
     * @return bool
     * @throws ApiException
     */
    public function updateAdminRole($param, $id)
    {
        if (empty($param)) {
            throw new ApiException('你没有做任何修改');
        }

        $res  =$this->adminRoleRepository->getOneById($id);
        if (!$res) {
            throw new ApiException('用户角色不存在!');
        }

        //更新
        $this->adminRoleRepository->update($res, $param);

        return true;
    }

    /**
     * 删除角色
     *
     * @param int $id 角色ID
     * @return bool
     * @throws ApiException
     */
    public function deleteAdminRole($id)
    {
        return $this->adminRoleRepository->deleteAdminRole($id);
    }
}
