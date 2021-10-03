<?php
namespace App\Logic\Api;

use Taoran\Laravel\Exception\ApiException;

class ArticleLogic
{
    /**
     * 获取列表
     *
     * @param $data
     * @return mixed
     * @throws ApiException
     */
    public static function getList($data)
    {
        $list = \App\Model\ArticleModel::select(['id', 'title', 'click_num', 'author', 'cover', 'tags', 'cat_id', 'is_show', 'desc', 'created_at', 'updated_at'])
            ->where('is_on', 1)
            ->where('is_show', 1)
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
            $item->tags = explode(',', $item->tags);
            $item->cover = 'https://baseran2.oss-cn-shenzhen.aliyuncs.com/default/style/bishi.jpg';
            $item->format_updated_at = get_msec_to_mescdate($item->updated_at);
        });

        return $list;
    }

    /**
     * 获取文章详情
     *
     * @param $id
     * @return mixed
     * @throws ApiException
     */
    public static function getOne($id)
    {
        $data = \App\Model\ArticleModel::select(['id', 'title', 'click_num', 'author', 'cover', 'tags', 'content', 'is_show', 'desc', 'created_at', 'updated_at'])
            ->where('is_on', 1)
            ->where('is_show', 1)
            ->find($id);

        if (!$data) {
            throw new ApiException("文章不存在！");
        }

        $data->tags = explode(',', $data->tags);
        $data->cover = 'https://baseran2.oss-cn-shenzhen.aliyuncs.com/default/style/bishi.jpg';
        $data->format_updated_at = get_msec_to_mescdate($data->updated_at);

        return $data;
    }
}
