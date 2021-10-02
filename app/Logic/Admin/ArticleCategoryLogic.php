<?php
namespace App\Logic\Admin;

use App\Exceptions\ApiException;

class ArticleCategoryLogic
{

    /**
     * 分类列表
     * @return \App\Model\ArticleTagModel|array|\Illuminate\Database\Query\Builder
     */
    public static function getArticleCategoryList()
    {
        $list = \App\Model\ArticleCategoryModel::where('is_on', '=', '1')
            ->select(['id', 'name', 'level', 'article_num', 'parent_id', 'updated_at', 'created_at'])
            ->orderBy('level')
            ->paginate(1);

        /*$new_list = [];
        $child_list = [];

        $list->each(function ($item, $key) use (&$new_list, &$child_list) {
            if ($item->parent_id == 0) {
                $new_list[$item->id] = $item->toArray();
            } else {
                if ($item->level == 2) {
                    $item->name = '|__ ' . $item->name;
                    $child_list[$item->id] = $item->toArray();
                } elseif ($item->level == 3) {
                    $item->name = '|____ ' . $item->name;
                    $child_list[$item->parent_id]['child'][] = $item->toArray();
                }
            }
        });*/


        /*$return_list = [];

        foreach ($new_list as $key => $v) {
            $return_list[] = $v;

            foreach ($child_list as $key2 => $vv) {
                if ($vv['parent_id'] == $v['id']) {

                    $child = array();

                    if (isset($vv['child'])) {
                        $child = $vv['child'];
                        unset($vv['child']);
                    }

                    $return_list[] = $vv;

                    if (!empty($child)) {
                        foreach ($child as $vvv) {
                            $return_list[] = $vvv;
                        }
                    }
                    unset($child_list[$key2]);
                }
            }
        }*/

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
        $data = \App\Model\ArticleCategoryModel::where('is_on', '=', '1')
            ->select(['id', 'name', 'parent_id', 'article_num'])
            ->find($id);

        if (empty($data)) {
            throw new ApiException('分类不存在!');
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
        //判断父级id
        if (isset($data['parent_id'])) {
            $parent = \App\Model\ArticleCategoryModel::where('is_on', '=', '1')
                ->select(['id', 'name', 'level', 'parent_id', 'article_num'])
                ->find($data['parent_id']);

            if (!$parent) {
                throw new ApiException('父级分类不存在!');
            }

            if ($parent->level == 3) {
                throw new ApiException('父级分类已经第三级,不能再添加子级!');
            }

            $data['level'] = $parent->level + 1;
        }
        else {
            $data['level'] = 1;
        }

        $article_category_model = new \App\Model\ArticleCategoryModel();
        set_save_data($article_category_model, $data);
        $res = $article_category_model->save();
        if (!$res) {
            throw new ApiException('添加分类失败');
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
        $res = \App\Model\ArticleCategoryModel::where('is_on', '=', 1)
            ->select(['id', 'name', 'parent_id'])
            ->find($id);

        if (!$res) {
            throw new ApiException('分类不存在!');
        }

        set_save_data($res, $data);

        $update_res = $res->save($data);
        if (!$update_res) {
            throw new ApiException('修改分类失败!');
        }
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
        //是否存在
        $res = \App\Model\ArticleCategoryModel::where('is_on', 1)
            ->select('id', 'parent_id', 'level')
            ->find($id);

        if (!$res) {
            throw new ApiException('商品分类不存在!');
        }

        //查询是否有下级分类
        if ($res->level == 1) {
            $is_parent = \App\Model\ArticleCategoryModel::where('is_on', 1)
                ->where('parent_id', $res->id)
                ->select('id', 'parent_id')
                ->first();

            if ($is_parent) {
                throw new ApiException('请先删除下级分类!');
            }
        }

        //判断是否有该分类文章
        $is_article = $res->articles()->where('article.is_on', 1)->first();
        if ($is_article) {
            throw new ApiException('该分类下还有文章,请先删除文章!');
        }

        set_save_data($res, ['is_on' => 0]);
        $update = $res->save();

        if (!$update) {
            \DB::rollBack();
            throw new ApiException('删除文章失败');
        }
        \DB::commit();
        return true;
    }

}
