<?php


namespace App\Repositorys;


use App\Exceptions\ApiException;
use App\Model\ArticleCategoryModel;
use Illuminate\Support\Facades\DB;

class ArticleCategoryRepository extends Repository
{
    protected $articleCategory;

    public function __construct()
    {
        $this->articleCategory = new ArticleCategoryModel();
    }

    /**
     * 获取列表
     *
     * @return mixed
     */
    public function getList()
    {
        return $this->articleCategory->where('is_on', 1)->paginate();
    }

    /**
     * 获取单条
     *
     * @param $id
     * @return mixed
     */
    public function getOneById($id)
    {
        return $this->articleCategory->where('is_on', 1)->find($id);
    }

    /**
     * 新增
     *
     * @param $param
     * @throws ApiException
     */
    public function add($param)
    {
        set_save_data($this->articleCategory, $param);
        $res = $this->articleCategory->save();
        if (!$res) {
            DB::rollBack();
            throw new ApiException('数据库错误！');
        }
        return true;
    }

    /**
     * 更新
     *
     * @param $model
     * @param $param
     * @return bool
     * @throws ApiException
     */
    public function update($model, $param)
    {
        set_save_data($model, $param);
        $res = $this->articleCategory->save();
        if (!$res) {
            DB::rollBack();
            throw new ApiException('数据库错误！');
        }
        return true;
    }

    /**
     * 删除
     *
     * @param $id
     * @return bool
     * @throws ApiException
     */
    public function delete($id)
    {
        $info = $this->articleCategory->getOneById($id);
        if (!$info) {
            DB::rollBack();
            throw new ApiException('数据不存在！');
        }

        $info->is_on = 1;
        $res = $info->save();
        if (!$res) {
            DB::rollBack();
            throw new ApiException();
        }

        return true;
    }
}