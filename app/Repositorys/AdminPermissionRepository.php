<?php
namespace App\Repositorys;

use App\Model\AdminPermissionModel;
use Taoran\Laravel\Repository;
use Taoran\Laravel\Exception\ApiException;

class AdminPermissionRepository extends Repository
{
    protected $model;

    public function __construct()
    {
        $this->model = new AdminPermissionModel();
    }

    public function delete($id)
    {
        $res = $this->getOneById($id);
        if (!$res) {
            throw new ApiException('数据不存在!');
        }

        $this->update($res, [
            'is_on' => 0
        ]);

        return true;
    }
}
