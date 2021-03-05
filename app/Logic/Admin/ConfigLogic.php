<?php

namespace App\Logic\Admin;

use App\Exceptions\ApiException;
use App\Exceptions\DatabaseException;

class ConfigLogic
{

    /**
     * 列表
     * @param array $data 列表查询条件
     *              $data.
     * @return
     */
    public static function getConfigList()
    {
        $list = \App\Model\ConfigModel::select(['id', 'code', 'desc', 'value', 'unit'])
            ->where('is_on', 1)
            ->where('is_show', 1)
            ->paginate(15);

        return $list;
    }

    /**
     * 修改信息
     * @param array $data 修改的信息
     * @param int $id ID
     * @return bool
     * @throws ApiException
     */
    public static function updateConfig($data, $id)
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->select(['id'])
            ->find($id);

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }

        set_save_data($res, $data);
        $update = $res->save();
        if (!$update) {
            throw new DatabaseException();
        }

        return true;
    }

    /**
     * 获取关于我们
     * @param $data
     * @return \App\Model\ConfigModel|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object
     * @throws ApiException
     */
    public static function getAboutUs()
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'about_us')
            ->select('value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }
        return $res;
    }

    /**
     * 更新关于我们
     * @param $data
     * @return bool
     * @throws ApiException
     */
    public static function putAboutUs($data)
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'about_us')
            ->select('id', 'value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }

        set_save_data($res, $data, [], ['value']);
        $update = $res->save();
        if (!$update) {
            throw new DatabaseException();
        }

        return true;
    }

    /**
     * 获取用户协议
     * @param $data
     * @return \App\Model\ConfigModel|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object
     * @throws ApiException
     */
    public static function getUserAgreement()
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'user_agreement')
            ->select('value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }
        return $res;
    }

    /**
     * 更新用户协议
     * @param $data
     * @return bool
     * @throws ApiException
     */
    public static function putUserAgreement($data)
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'user_agreement')
            ->select('id', 'value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }

        set_save_data($res, $data, [], ['value']);
        $update = $res->save();
        if (!$update) {
            throw new DatabaseException();
        }

        return true;
    }

    /**
     * 获取隐私政策
     * @param $data
     * @return \App\Model\ConfigModel|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object
     * @throws ApiException
     */
    public static function getPrivacy()
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'privacy')
            ->select('value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }
        return $res;
    }

    /**
     * 更新隐私政策
     * @param $data
     * @return bool
     * @throws ApiException
     */
    public static function putPrivacy($data)
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'privacy')
            ->select('id', 'value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }

        set_save_data($res, $data, [], ['value']);
        $update = $res->save();
        if (!$update) {
            throw new DatabaseException();
        }

        return true;
    }
    
    /**
     *
     * @param $data
     * @throws ApiException
     * @throws \Exception
     */
    public static function updateBaseSetting($data)
    {
        load_helper('File');
        if (isset($data['base_setting_loginbg']) && $data['base_setting_loginbg'] !== '') {
            $admin_id = \Jwt::get('admin_info.admin_id');
            $image = \App\Model\UploadModel::where('is_on', 1)
                ->where('admin_id', $admin_id)
                ->find($data['base_setting_loginbg']);
            if (!$image) {
                throw new ApiException('图片不存在!');
            }
            $data['base_setting_loginbg'] = auto_url($image->path ?? '');
        }

        if (isset($data['base_setting_favicon']) && $data['base_setting_favicon'] !== '') {
            $admin_id = \Jwt::get('admin_info.admin_id');
            $image = \App\Model\UploadModel::where('is_on', 1)
                ->where('admin_id', $admin_id)
                ->find($data['base_setting_favicon']);
            if (!$image) {
                throw new ApiException('图片不存在!');
            }
            $data['base_setting_favicon'] = auto_url($image->path ?? '');
        }

        if (isset($data['base_setting_topnav_bg']) && $data['base_setting_topnav_bg'] !=='') {
            $admin_id = \Jwt::get('admin_info.admin_id');
            $image = \App\Model\UploadModel::where('is_on', 1)
                ->where('admin_id', $admin_id)
                ->find($data['base_setting_topnav_bg']);
            if (!$image) {
                throw new ApiException('图片不存在!');
            }
            $data['base_setting_topnav_bg'] = $image->path;
        }

        if (isset($data['base_setting_sidebar_bg']) && $data['base_setting_sidebar_bg'] !=='' ) {
            $admin_id = \Jwt::get('admin_info.admin_id');
            $image = \App\Model\UploadModel::where('is_on', 1)
                ->where('admin_id', $admin_id)
                ->find($data['base_setting_sidebar_bg']);
            if (!$image) {
                throw new ApiException('图片不存在!');
            }
            $data['base_setting_sidebar_bg'] = $image->path;
        }

        \DB::beginTransaction();

        foreach ($data as $key => $value) {
            $update = \App\Model\ConfigModel::where('is_on', 1)
                ->where('code', $key)
                ->update(['value' => $value]);
            if (!$update) {
                \DB::rollBack();
                throw new DatabaseException();
            }
        }

        \DB::commit();
    }

    /**
     * 获取退换货说明
     * @param $data
     * @return \App\Model\ConfigModel|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object
     * @throws ApiException
     */
    public static function getReturnDesc()
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'return_desc')
            ->select('value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }
        return $res;
    }

    /**
     * 更新退换货说明
     * @param $data
     * @return bool
     * @throws ApiException
     */
    public static function putReturnDesc($data)
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'return_desc')
            ->select('id', 'value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }

        set_save_data($res, $data, [], ['value']);
        $update = $res->save();
        if (!$update) {
            throw new DatabaseException();
        }

        return true;
    }

    /**
     * 获取基础分销比例
     *
     * @return \App\Model\ConfigModel|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public static function getBenefitRatio()
    {
        $codes = [
            'base_first_benefit_ratio',
            'base_second_benefit_ratio'
        ];
        $data = \App\Model\ConfigModel::where('is_on', 1)
            ->select(['id', 'code', 'value'])
            ->whereIn('code', $codes)
            ->get();

        //重组数据
        $new_data = [];
        $data->each(function ($item) use (&$new_data) {
            $new_data[$item->code] = $item->value;
        });

        return $new_data;
    }
    /**
     * 设置基础分销比例
     */
    public static function setBenefitRatio($data)
    {
        \DB::beginTransaction();

        //设置一推分销比例
        if (isset($data['base_first_benefit_ratio'])) {

            $res = \App\Model\ConfigModel::where('is_on', 1)->where('code', 'base_first_benefit_ratio')->update([
                'value' => $data['base_first_benefit_ratio']
            ]);

            if (!$res) {
                \DB::rollBack();
                throw new DatabaseException();
            }
        }

        //设置二推分销比例
        if (isset($data['base_second_benefit_ratio'])) {
            $res = \App\Model\ConfigModel::where('is_on', 1)->where('code', 'base_second_benefit_ratio')->update([
                'value' => $data['base_second_benefit_ratio']
            ]);

            if (!$res) {
                \DB::rollBack();
                throw new DatabaseException();
            }
        }

        \DB::commit();

        return $data;
    }

    /**
     * 获取微信注册完善资料设置
     *
     * @return \App\Model\ConfigModel|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public static function getWechatRegisterPerfectInfo()
    {
        $data = \App\Model\ConfigModel::where('is_on', 1)
            ->select(['id', 'code', 'value'])
            ->where('code', 'wechat_register_perfect_info')
            ->first();

        //重组数据
        $data->value = json_decode($data['value']);

        return $data['value'];
    }

    /**
     * 设置微信注册完善资料设置
     *
     * @return \App\Model\ConfigModel|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public static function setWechatRegisterPerfectInfo($data)
    {
        if ($data['phone_show'] == 0) {
            $data['phone_require'] = 0;
        }

        if ($data['password_show'] == 0) {
            $data['password_require'] = 0;
        }

        if ($data['invite_code_show'] == 0) {
            $data['invite_code_require'] = 0;
        }

        $data = json_encode($data);

        $res = \App\Model\ConfigModel::where('code', 'wechat_register_perfect_info')
            ->update([
                'value' => $data
            ]);

        if (!$res) {
            throw new DatabaseException();
        }

        $cache_key = 'config:wechat:register:perfect:info';
        \Cache::forget($cache_key);

        return true;
    }


    /**
     * 获取优惠券规则说明
     * @param $data
     * @return \App\Model\ConfigModel|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object
     * @throws ApiException
     */
    public static function getCouponDesc()
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'coupon_desc')
            ->select('value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }
        return $res;
    }

    /**
     * 更新优惠券规则说明
     * @param $data
     * @return bool
     * @throws ApiException
     */
    public static function putCouponDesc($data)
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'coupon_desc')
            ->select('id', 'value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }

        set_save_data($res, $data, [], ['value']);
        $update = $res->save();
        if (!$update) {
            throw new DatabaseException();
        }

        return true;
    }

    /**
     * 获取秒杀活动规则说明
     * @param $data
     * @return \App\Model\ConfigModel|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object
     * @throws ApiException
     */
    public static function getSeckillDesc()
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'seckill_desc')
            ->select('value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }
        return $res;
    }

    /**
     * 更新秒杀活动规则说明
     * @param $data
     * @return bool
     * @throws ApiException
     */
    public static function putSeckillDesc($data)
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'seckill_desc')
            ->select('id', 'value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }

        set_save_data($res, $data, [], ['value']);
        $update = $res->save();
        if (!$update) {
            throw new DatabaseException();
        }

        return true;
    }

    /**
     * 获取秒杀活动规则说明
     * @param $data
     * @return \App\Model\ConfigModel|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object
     * @throws ApiException
     */
    public static function getIntegralDesc()
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'integral_desc')
            ->select('value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }
        return $res;
    }

    /**
     * 更新秒杀活动规则说明
     * @param $data
     * @return bool
     * @throws ApiException
     */
    public static function putIntegralDesc($data)
    {
        //是否存在
        $res = \App\Model\ConfigModel::where('is_on', 1)
            ->where('code', 'integral_desc')
            ->select('id', 'value')
            ->first();

        if (!$res) {
            throw new ApiException('配置信息不存在!');
        }

        set_save_data($res, $data, [], ['value']);
        $update = $res->save();
        if (!$update) {
            throw new DatabaseException();
        }

        return true;
    }
}
