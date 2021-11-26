<?php
namespace App\Logic\Admin;

use Taoran\Laravel\Exception\ApiException;

class ArticleLogic
{

    /**
     * 文章列表
     * @param array $data 列表查询条件
     *              $data.cat_id 分类id
     *              $data.article_tag 标签id,逗号分隔
     *              $data.title 标题
     *              $data.is_show 是否显示
     *              $data.author 作者
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public static function getArticleList($data)
    {
        $list = \App\Model\ArticleModel::select(['id', 'title', 'click_num', 'author', 'cover', 'tags', 'cat_id', 'is_show', 'created_at', 'updated_at'])
            ->where('is_on', 1)
//            ->with(['tags' => function ($query) use ($data) {
//                $query->where('is_on', 1)->select('name');
//            }])
            ->orderBy('id', 'Desc');

        //筛选tag
        if (isset($data['article_tag'])) {
            $tags = explode(',', $data['article_tag']);
            $list->whereHas('tags', function ($query) use ($tags) {
                $query->whereIn('tag_id', $tags);
            });
        }

        //筛选作者
        if (isset($data['author'])) {
            $list->where('author', $data['author']);
        }

        //筛选标题
        if (isset($data['title'])) {
            $list->where('title', 'like', '%' . $data['title'] . '%');
        }

        //筛选是否显示
        if (isset($data['is_show'])) {
            $list->where('is_show', $data['is_show']);
        }

        //筛选创建时间
        if (isset($data['time'][0]) && isset($data['time'][1])) {
            if ($data['time'][0] > $data['time'][1]) {
                throw new ApiException('开始时间不能大于结束时间');
            }

            $list->whereBetween('created_at', [$data['time'][0], $data['time'][1]]);
        }

        $list = $list->paginate();

        //重装数据
        $list->each(function ($item, $key) {
            $item->is_show_text = \App\Model\ArticleModel::IS_SHOW[$item->is_show];
            $item->tags = explode(',', $item->tags);
            //$item->tags_name = $item->tags->implode('name', ',');
            //$item->cover = auto_url($item->cover);
            //unset($item->tags);
        });

        return $list;
    }

    /**
     * 获取文章详情
     * @param int $id 文章ID
     * @return \App\Model\ArticleModel|array|\Illuminate\Database\Query\Builder|null|\stdClass
     * @throws ApiException
     */
    public static function getOneArticle($id)
    {
        $data = \App\Model\ArticleModel::where('is_on', '=', 1)
            ->select(['id', 'title', 'author', 'cover', 'is_show', 'click_num', 'tags', 'cat_id', 'content', 'created_at', 'updated_at'])
            ->find($id);

        if (!$data) {
            throw new ApiException('文章不存在!', 'NO_ARTICLE');
        }

        $data->tags = explode(',', $data->tags);
        $data->content = htmlspecialchars_decode($data->content, ENT_QUOTES);

        return $data;
    }

    /**
     * 添加文章
     * @param array $data 要添加的文章数据
     * @return bool
     * @throws ApiException
     * @throws \Exception
     */
    public static function addArticle($data)
    {
        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = trim(implode(',', $data['tags']), ',');
        }
        $article_model = new \App\Model\ArticleModel();
        $data['content_html'] = self::markdownToHtml($data['content']);
        unset($data['token']);
        set_save_data($article_model, $data);
        $res = $article_model->save();
        if (!$res) {
            \DB::rollBack();
            throw new ApiException('添加文章失败!');
        }
        return true;
    }

    /**
     * 修改文章信息
     * @param array $data 修改的信息
     * @param int $id 文章ID
     * @return bool
     * @throws ApiException
     * @throws \Exception
     */
    public static function updateArticle($data, $id)
    {
        //文章是否存在
        $res = \App\Model\ArticleModel::where('is_on', '=', 1)->find($id);

        if (!$res) {
            throw new ApiException('文章不存在!');
        }

        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = trim(implode(',', $data['tags']), ',');
        }

        \DB::beginTransaction();
        $data['content_html'] = self::markdownToHtml($data['content']);
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
     * 删除文章
     * @param int $id 文章ID
     * @return bool
     * @throws ApiException
     * @throws \Exception
     */
    public static function deleteArticle($id)
    {
        $res = \App\Model\ArticleModel::where('is_on', '=', 1)
            ->select(['id'])
            ->find($id);

        if (!$res) {
            throw new ApiException('文章不存在');
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

    /**
     * markdown to html
     *
     * @param $markdown
     * @return mixed
     */
    public static function markdownToHtml($markdown)
    {
        // markdown to html
        $convertedHmtl = app('Parsedown')->setBreaksEnabled(true)->text($markdown);

        // 代码高亮展示优化
        $convertedHmtl = str_replace("<pre><code>", '<pre><code class=" language-php">', $convertedHmtl);

        // 移除 {{}}
        // $convertedHmtl = remove_vue($convertedHmtl);

        // 返回 html
        return $convertedHmtl;
    }

}
