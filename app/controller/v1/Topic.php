<?php

namespace app\controller\v1;

use app\BaseController;
use app\model\Topic as TopicModel;
use app\validate\TopicValidate;

/**
 * Class Topic
 * @package app\controller\v1
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Topic extends BaseController
{
    /**
     * 获取专题列表
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list()
    {
        (new TopicValidate())->goCheck('list');
        $data = (new TopicModel())->list();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 获取热门专题
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function hits()
    {
        (new TopicValidate())->goCheck('hits');
        $data = (new TopicModel())->getHits();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 获取专题详情
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail()
    {
        (new TopicValidate())->goCheck('detail');
        $data = (new TopicModel())->detail();
        return self::showResCode('获取成功', $data);
    }
}