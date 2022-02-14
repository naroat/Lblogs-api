<?php

namespace App\Logic\Admin;

use Taoran\Laravel\Exception\ApiException;

class NavCategoryLogic
{
    public static function getList($data)
    {
        $list = \App\Model\NavCategoryModel::where('is_on', 1)
//            ->with(['tags' => function ($query) use ($data) {
//                $query->where('is_on', 1)->select('name');
//            }])
                ->orderBy('id', 'Desc');


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

    public static function getOne($id)
    {
        $data = \App\Model\NavCategoryModel::where('is_on', '=', 1)
            ->find($id);

        if (!$data) {
            throw new ApiException('数据不存在!', 'NO_ARTICLE');
        }

        return $data;

    }

    public static function add($data)
    {
        $model = new \App\Model\NavCategoryModel();
        unset($data['token']);
        set_save_data($model, $data);

        $res = $model->save();

        if (!$res) {
            \DB::rollBack();
            throw new ApiException('添加失败!');
        }
        return true;

    }

    public static function update($data, $id)
    {

        //文章是否存在
        $res = \App\Model\NavCategoryModel::where('is_on', '=', 1)->find($id);

        if (!$res) {
            throw new ApiException('数据不存在!');
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

    public static function delete($id)
    {
        $res = \App\Model\NavCategoryModel::where('is_on', '=', 1)
            ->select(['id'])
            ->find($id);

        if (!$res) {
            throw new ApiException('数据不存在');
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
