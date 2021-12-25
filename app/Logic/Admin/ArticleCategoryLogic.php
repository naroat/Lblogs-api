<?php
namespace App\Logic\Admin;

use App\Exceptions\ApiException;

class ArticleCategoryLogic
{

    /**
     * 分类列表
     * @return \App\Model\ArticleTagModel|array|\Illuminate\Database\Query\Builder
     */
    public static function getArticleCategoryList($data)
    {
        $list = \App\Model\ArticleCategoryModel::where('is_on', 1)
//            ->with(['tags' => function ($query) use ($data) {
//                $query->where('is_on', 1)->select('name');
//            }])
            ->orderBy('id', 'Desc');


        //筛选标题
        if (isset($data['name'])) {
            $list->where('name', 'like', '%' . $data['name'] . '%');
        }

        //筛选创建时间
        if (isset($data['time'][0]) && isset($data['time'][1])) {
            if ($data['time'][0] > $data['time'][1]) {
                throw new \Taoran\Laravel\Exception\ApiException('开始时间不能大于结束时间');
            }

            $list->whereBetween('created_at', [$data['time'][0], $data['time'][1]]);
        }

        if (isset($data['is_all']) && $data['is_all'] == 1) {
            $list = $list->get();
        } else {
            $list = $list->paginate();
        }


        //重装数据
        /*$list->each(function ($item, $key) {
        });*/

        return $list;
    }

    /**
     * 分类单条数据
     * @param int $id 分类ID
     * @return \App\Model\ArticleTagModel|array|\Illuminate\Database\Query\Builder|null|\stdClass
     * @throws ApiException
     */
    public static function getOneArticleCategory($id)
    {
        $data = \App\Model\ArticleCategoryModel::where('is_on', '=', 1)
            ->find($id);

        if (!$data) {
            throw new \Taoran\Laravel\Exception\ApiException('数据不存在!', 'NO_ARTICLE');
        }

        return $data;
    }

    /**
     * 添加分类
     * @param array $data 要添加的信息
     * @return bool
     * @throws ApiException
     */
    public static function addArticleCategory($data)
    {
        $model = new \App\Model\ArticleCategoryModel();
        unset($data['token']);
        set_save_data($model, $data);

        $res = $model->save();

        if (!$res) {
            \DB::rollBack();
            throw new \Taoran\Laravel\Exception\ApiException('添加失败!');
        }
        return true;
    }

    /**
     * 分类修改
     * @param array $data 要修改的数据
     * @param int $id 分类ID
     * @return bool
     * @throws ApiException
     */
    public static function updateArticleCategory($data, $id)
    {
        //文章是否存在
        $res = \App\Model\ArticleCategoryModel::where('is_on', '=', 1)->find($id);

        if (!$res) {
            throw new \Taoran\Laravel\Exception\ApiException('数据不存在!');
        }


        \DB::beginTransaction();
        //更新主表
        set_save_data($res, $data);
        $update = $res->save();
        if (!$update) {
            \DB::rollBack();
            throw new ApiException();
        }

        \DB::commit();
        return true;
    }

    /**
     * 分类删除
     * @param int $id 分类ID
     * @return bool
     * @throws ApiException
     * @throws \Exception
     */
    public static function deleteArticleCategory($id)
    {
        $res = \App\Model\ArticleCategoryModel::where('is_on', '=', 1)
            ->select(['id'])
            ->find($id);

        if (!$res) {
            throw new \Taoran\Laravel\Exception\ApiException('数据不存在');
        }

        \DB::beginTransaction();

        set_save_data($res, ['is_on' => 0]);
        $update = $res->save();

        if (!$update) {
            \DB::rollBack();
            throw new DatabaseException();
        }

        \DB::commit();

        return true;
    }

}
