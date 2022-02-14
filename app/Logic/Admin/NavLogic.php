<?php

namespace App\Logic\Admin;

use Illuminate\Support\Facades\DB;
use Taoran\Laravel\Exception\ApiException;

class NavLogic
{
    public static function getList($data)
    {
        $list = \App\Model\NavModel::where('is_on', 1)
//            ->with(['tags' => function ($query) use ($data) {
//                $query->where('is_on', 1)->select('name');
//            }])

            ->with('navCategorys')
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
        $list->each(function ($item, $key) {
            $titles = $item->navCategorys->pluck('title');
            $item->cate_text = trim(implode(',', $titles->toArray()), ',');
        });

        return $list;
    }

    public static function getOne($id)
    {
        $data = \App\Model\NavModel::where('is_on', '=', 1)
            ->with('navCategorys')
            ->find($id);

        if (!$data) {
            throw new ApiException('数据不存在!', 'NO_ARTICLE');
        }

        $cate_ids = $data->navCategorys->pluck('id');
        $data->cate_ids = $cate_ids->toArray();

        return $data;

    }

    public static function add($data)
    {
        $cate_ids = $data['cate_ids'] ?? [];
        unset($data['cate_ids']);

        DB::beginTransaction();

        $model = new \App\Model\NavModel();
        unset($data['token']);
        set_save_data($model, $data);
        $res = $model->save();
        if (!$res) {
            DB::rollBack();
            throw new ApiException('添加失败!');
        }

        if (count($cate_ids) > 0) {
            //绑定导航和导航分类
            $save_data = [];
            foreach ($cate_ids as $v) {
                $save_data[] = [
                  'nav_id' => $model->id,
                  'nav_category_id' => $v,
                ];
            }
            $res = \App\Model\NavNavCategoryModel::insert($save_data);

            if (!$res) {
                DB::rollBack();
                throw new ApiException('数据库错误!');
            }
        }


        DB::commit();


        return true;

    }

    public static function update($data, $id)
    {

        $cate_ids = $data['cate_ids'] ?? [];
        unset($data['cate_ids']);

        //是否存在
        $res = \App\Model\NavModel::where('is_on', '=', 1)->find($id);

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


        if (count($cate_ids) > 0) {
            //删除原数据
            \App\Model\NavNavCategoryModel::where('nav_id', $res->id)->delete();

            //绑定导航和导航分类
            $save_data = [];
            foreach ($cate_ids as $v) {
                $save_data[] = [
                    'nav_id' => $res->id,
                    'nav_category_id' => $v,
                ];
            }
            $res = \App\Model\NavNavCategoryModel::insert($save_data);

            if (!$res) {
                DB::rollBack();
                throw new ApiException('数据库错误!');
            }
        }

        \DB::commit();
        return true;

    }

    public static function delete($id)
    {
        $res = \App\Model\NavModel::where('is_on', '=', 1)
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
