<?php
declare (strict_types=1);

namespace app\validate;

use app\lib\exception\BaseException;

use app\model\Art;
use app\model\Topic;
use app\model\Ulog;
use app\model\Vod;
use think\Validate;

/**
 * Class BaseValidate
 * @package app\validate
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class BaseValidate extends Validate
{
    /**
     * 通用数据验证 支持验证场景
     * @param string $scene 验证场景
     * @return bool
     * @throws BaseException
     */
    public function goCheck(string $scene = ''): bool
    {
        //获取所有请求参数
        $params = input();
        //是否需要验证场景
        $check = $scene ? $this->scene($scene)->check($params) : $this->check($params);
        if (!$check) {
            ApiException($this->getError(), 10000, 400);
        }
        return true;
    }


    /**
     * 验证验证码
     * @param string $value
     * @param string $rule
     * @param array $data
     * @param string $field
     * @return bool|string
     */
    protected function isRightCode(string $value, string $rule = '', array $data = [], string $field = '')
    {
        $beforeCode = cache('sendCode_' . $data['phone']);
        if (!$beforeCode) return '验证码不存在';
        //验证验证码
        if ($value != $beforeCode) return '验证码错误';
        return true;
    }

    /**
     * 判断影片是否存在
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isVodExist($value, $rule = '', $data = '', $field = '')
    {
        if ($value == 0) return true;
        if (Vod::field('vod_id')->find($value)) {
            return true;
        }
        return "影片不存在";
    }

    /**
     * 判断资讯是否存在
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isArtExist($value, $rule = '', $data = '', $field = '')
    {
        if ($value == 0) return true;
        if (Art::field('art_id')->find($value)) {
            return true;
        }
        return "资讯不存在";
    }

    /**
     * 判断浏览记录是否存在
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isUlogExist($value, $rule = '', $data = '', $field = '')
    {
        if ($value == 0) return true;
        if (strpos($value, ',') !== false) {
            foreach (explode(',', $value) as $item) {
                if (!Ulog::field('ulog_id')->find($item)) {
                    return "浏览记录{$item}不存在";
                }
            }
            return true;
        }
        if (Ulog::field('ulog_id')->find($value)) {
            return true;
        }
        return "浏览记录不存在";
    }

    /**
     * 判断专题是否存在
     * @param $value
     * @param string $rule
     * @param string $data
     * @param string $field
     * @return bool|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function isTopicExist($value, $rule = '', $data = '', $field = '')
    {
        if ($value == 0) return true;
        if (Topic::field('topic_id')->find($value)) {
            return true;
        }
        return "专题不存在";
    }

}
