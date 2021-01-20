<?php


namespace App\Repositorys;


use App\Model\AdminUserModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\Expr\AssignOp\Mod;
use Taoran\Laravel\Exception\ApiException;
use Taoran\Laravel\Repository;

class AdminUserRepository extends Repository
{
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
            if (isset($param['account'])) {
                $query->where('account', $param['account']);
            }
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
            $query->with(['roles' => function ($query) {
                $query->select('name');
            }])->orderBy('id', 'DESC');

            //筛选名称
            if (isset($param['name'])) {
                $query->where('name', 'like', '%' . $param['name'] . '%');
            }

            //筛选手机号码
            if (isset($param['phone'])) {
                $query->where('phone', $param['phone']);
            }

            //筛选创建时间
            if (isset($param['start_time']) && isset($param['end_time'])) {
                if ($param['start_time'] > $param['end_time']) {
                    throw new ApiException('开始时间不能大于结束时间');
                }
                $query->whereBetween('created_at', [$param['start_time'], $param['end_time']]);
            }
        });
    }
}
