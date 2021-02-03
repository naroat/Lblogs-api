<?php


namespace App\Services;


use App\Repositorys\AdminMenuGroupRepository;
use Taoran\Laravel\Exception\ApiException;

class AdminMenuGroupService
{
    protected $adminMenuGroupRepository;

    public function __construct()
    {
        $this->adminMenuGroupRepository = new AdminMenuGroupRepository();
    }

    public function getList($param)
    {
        return $this->adminMenuGroupRepository->getList($param);
    }

    public function add($param)
    {
        return $this->adminMenuGroupRepository->create($param);
    }

    public function getOne($id)
    {
        $data = $this->adminMenuGroupRepository->getOneById($id);
        if (!$data) {
            throw new ApiException('数据不存在!');
        }
        return $data;
    }

    public function edit($param, $id)
    {
        if (empty($param)) {
            throw new ApiException('你没有做任何修改');
        }

        $res  =$this->adminMenuGroupRepository->getOneById($id);
        if (!$res) {
            throw new ApiException('用户角色不存在!');
        }

        //更新
        $this->adminMenuGroupRepository->update($res, $param);

        return true;
    }

    public function delete($id)
    {
        return $this->adminMenuGroupRepository->deleteAdminMenuGroup($id);
    }
}
