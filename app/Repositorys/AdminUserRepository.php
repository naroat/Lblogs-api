<?php


namespace App\Repositorys;


use App\Model\AdminUserModel;
use App\Packages\Core\src\Repository\Repository;
use App\Packages\Core\src\Traits\CallbackTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\AssignOp\Mod;

class AdminUserRepository extends Repository
{
    use CallbackTrait;

    protected $model;

    public function __construct()
    {
        $this->model = new AdminUserModel();
    }

    /**
     * 获取单条
     *
     * @param array $param
     * @return mixed
     */
    public function getAdminUserOne(array $param)
    {
        return $this->getOne(function ($query) use ($param) {
            //设置筛选条件
            $this->setConditions($query, $param);
        });
    }

    /**
     * 获取多条
     *
     * @param array $param
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Builder|Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAdminUserList(array $param)
    {
        return $this->getList($param, function ($query) use ($param) {
            //设置筛选条件
            $this->setConditions($query, $param);
        });
    }

    /**
     * 设置条件
     *
     * @param Builder $orm
     * @param array $param
     */
    public function setConditions(Builder $orm, array $param)
    {
        if (isset($param['id'])) {
            $orm->where('id', $param['id']);
        }

        if (isset($param['orderBy'])) {
            $orm->orderBy($param['orderBy'][0], $param['orderBy'][1]);
        }
    }
}
