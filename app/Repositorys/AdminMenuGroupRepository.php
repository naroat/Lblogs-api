<?php
namespace App\Repositorys;

use App\Model\AdminMenuGroupModel;
use Taoran\Laravel\Repository;
use Taoran\Laravel\Exception\ApiException;

class AdminMenuGroupRepository extends Repository
{
    protected $model;

    public function __construct()
    {
        $this->model = new AdminMenuGroupModel();
    }

    public function deleteAdminMenuGroup($id)
    {
        $res = $this->getOneById($id);
        if (!$res) {
            throw new ApiException('菜单组不存在!');
        }

        $this->update($res, [
            'is_on' => 0
        ]);

        return true;
    }
}
