<?php
namespace App\Logic\Admin;

use App\Model\FormElementModel;
use Taoran\Laravel\Exception\ApiException;

class FormLogic
{
    /**
     * 获取列表
     *
     * @param $params
     * @return mixed
     */
    public static function getList($params)
    {
        $list = \App\Model\FormModel::where('is_on', 1)
            ->with(['formElements' => function ($query) {
                $query->where('is_on', 1);
            }]);

        if (isset($params['title'])) {
            $list->where('title', 'like', '%' . $params['title'] . '%');
        }

        $list = $list->paginate();
        return $list;
    }

    /**
     * 获取单条
     */
    public static function getOne($id)
    {
        $data = \App\Model\FormModel::where('is_on', 1)
            ->with(['formElements' => function ($query) {
                $query->where('is_on', 1);
            }])
            ->find($id);

        return $data;
    }

    /**
     * 添加
     *
     * @param $params
     * @return bool
     * @throws ApiException
     */
    public static function addForm($params)
    {
        $form = \App\Model\FormModel::where('is_on', 1)->where('title', $params['title'])->first();
        if ($form) {
            throw new ApiException('表单已存在!');
        }

        \DB::beginTransaction();

        $formModel = new \App\Model\FormModel();
        set_save_data($formModel, [
            'title' => $params['title'],
            'description' => $params['description'] ?? '',
        ]);
        $res = $formModel->save();
        if  (!$res) {
            \DB::rollback();
            throw new ApiException();
        }

        //元素
        $insert_data = [];
        foreach ($params['element'] as $key => $val) {
            $insert_data[] = [
                'type' => $val['type'],
                'title' => $val['title'],
                'name' => $val['name'],
                'is_must' => $val['is_must'],
                'sort' => $val['sort'] ?? 1,
                'options' => $val['options'],
                'created_at' => get_msectime(),
                'updated_at' => get_msectime(),
            ];
        }

        $res = \DB::table('form_element')->insert($insert_data);
        if (!$res) {
            \DB::rollback();
            throw new ApiException();
        }

        \DB::commit();

        return true;
    }

    /**
     * 更新
     *
     * @param $params
     * @param $id
     */
    public static function updateForm($params, $id)
    {
        $form = \App\Model\FormModel::where('is_on', 1)->find($id);
        if (!$form) {
            throw new ApiException('表单不存在!');
        }

        //验证title是否重复
        $other_form_count = \App\Model\FormModel::where('is_on', 1)->where('id', '!=', $id)->where('title', $params['title'])->count();
        if ($other_form_count > 0) {
            throw new ApiException('表单名称重复!');
        }

        \DB::beginTransaction();

        set_save_data($form, [
            'title' => $params['title'],
            'description' => $params['description'] ?? '',
        ]);
        $res = $form->save();
        if  (!$res) {
            \DB::rollback();
            throw new ApiException();
        }

        //删除原元素
        $delElement = \App\Model\FormElementModel::where('is_on', 1)->where('form_id', $id)->update([
            'is_on' => 0
        ]);

        //添加元素
        $insert_data = [];
        foreach ($params['element'] as $key => $val) {
            $insert_data[] = [
                'form_id' => $id,
                'type' => $val['type'],
                'title' => $val['title'],
                'name' => $val['name'],
                'is_must' => $val['is_must'],
                'sort' => $val['sort'] ?? 1,
                'options' => $val['options'],
                'created_at' => get_msectime(),
                'updated_at' => get_msectime(),
            ];
        }

        $res = \DB::table('form_element')->insert($insert_data);
        if (!$res) {
            \DB::rollback();
            throw new ApiException();
        }

        \DB::commit();

        return true;
    }

    /**
     * 删除
     *
     * @param $id
     * @return bool
     * @throws ApiException
     */
    public static function deleteForm($id)
    {
        $form = \App\Model\FormModel::where('is_on', 1)->find($id);
        if (!$form) {
            throw new ApiException('表单不存在!');
        }

        \DB::beginTransaction();

        $form->is_on = 0;
        $res = $form->save();
        if (!$res) {
            \DB::rollback();
            throw new ApiException();
        }

        //删除元素
        $delElement = \App\Model\FormElementModel::where('is_on', 1)->where('form_id', $id)->update([
            'is_on' => 0
        ]);

        \DB::commit();
        return true;
    }
}
