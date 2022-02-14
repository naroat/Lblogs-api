<?php
namespace App\Logic\Admin;

use App\Exceptions\ApiException;

class ArticleTagLogic
{

    /**
     * 标签列表
     * @param $data array 筛选数据
     *        $data.name 筛选名称
     * @return \App\Model\ArticleTagModel|array|\Illuminate\Database\Query\Builder
     */
    public static function getArticleTagList($data)
    {
        $artcle_tag_model = \App\Model\ArticleTagModel::where('is_on', '=', '1')
            ->select(['id', 'name', 'updated_at', 'created_at'])
            ->orderBy('id', 'DESC');

        //筛选名称
        if (isset($data['name'])) {
            $artcle_tag_model->where('name', 'like', '%' . $data['name'] . '%');
        }

        if(isset($data['is_page']) && $data['is_page']==1){
            $list = $artcle_tag_model->paginate();
        }
        else{
            $list = $artcle_tag_model->get();
        }

        return $list;
    }

    /**
     * 标签单条数据
     * @param int $id 标签ID
     * @return \App\Model\ArticleTagModel|array|\Illuminate\Database\Query\Builder|null|\stdClass
     * @throws ApiException
     */
    public static function getOneArticleTag($id)
    {
        $res = \App\Model\ArticleTagModel::where('is_on', '=', '1')
            ->select(['id', 'name'])
            ->find($id);

        if (!$res) {
            throw new ApiException('文章分类不存在!');
        }

        return $res;
    }

    /**
     * 添加标签
     * @param array $data 要添加的信息
     * @return bool
     * @throws ApiException
     */
    public static function addArticleTag($data)
    {
        $article_tag_model = new \App\Model\ArticleTagModel();
        set_save_data($article_tag_model, $data);
        $res = $article_tag_model->save();
        if (!$res) {
            throw new ApiException('添加失败');
        }

        return true;
    }

    /**
     * 标签修改
     * @param array $data 要修改的数据
     * @param int $id 标签ID
     * @return bool
     * @throws ApiException
     */
    public static function updateArticleTag($data, $id)
    {
        $res = \App\Model\ArticleTagModel::where('is_on', '=', 1)
            ->select(['id', 'name'])
            ->find($id);

        if (!$res) {
            throw new ApiException('文章标签不存在!');
        }

        set_save_data($res, $data);
        $res->save($data);
        if (!$res) {
            throw new ApiException('修改失败');
        }

        return true;
    }

    /**
     * 标签删除
     * @param int $id 标签ID
     * @return bool
     * @throws ApiException
     * @throws \Exception
     */
    public static function deleteArticleTag($id)
    {
        $res = \App\Model\ArticleTagModel::where('is_on', '=', 1)
            ->select(['id'])
            ->find($id);

        if(!$res){
            throw new ApiException('文章标签不存在!');
        }

        //判断是否有文章是否
        $is_article = $res->articles()->select('article.id')->first();
        if($is_article){
            throw new ApiException('标签下还有文章,请将文章改换标签后再删除!');
        }


        set_save_data($res,['is_on' => 0]);
        $delete_article = $res->save();
        if (!$delete_article) {
            throw new ApiException('删除失败');
        }

        return true;
    }

}
