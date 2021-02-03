<?php


namespace App\Services;


use App\Repositorys\AdminMenuRepository;
use Taoran\Laravel\Exception\ApiException;

class AdminMenuService
{
    protected $adminMenuRepository;

    public function __construct()
    {
        $this->adminMenuRepository = new AdminMenuRepository();
    }

    public function getList($param)
    {
        return $this->adminMenuRepository->getList($param);
    }

    public function add($param)
    {
        return $this->adminMenuRepository->create($param);
    }

    public function getOne($id)
    {
        $data = $this->adminMenuRepository->getOneById($id);
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

        $res  =$this->adminMenuRepository->getOneById($id);
        if (!$res) {
            throw new ApiException('用户角色不存在!');
        }

        //更新
        $this->adminMenuRepository->update($res, $param);

        return true;
    }

    public function delete($id)
    {
        return $this->adminMenuRepository->deleteAdminMenu($id);
    }
}
