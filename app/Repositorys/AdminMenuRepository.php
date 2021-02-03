<?php
namespace App\Repositorys;

use App\Model\AdminMenuModel;
use Taoran\Laravel\Repository;
use Taoran\Laravel\Exception\ApiException;

class AdminMenuRepository extends Repository
{
    protected $model;

    public function __construct()
    {
        $this->model = new AdminMenuModel();
    }

    public function deleteAdminMenu($id)
    {
        $res = $this->getOneById($id);
        if (!$res) {
            throw new ApiException('菜单不存在!');
        }

        $this->update($res, [
            'is_on' => 0
        ]);

        return true;
    }
}
