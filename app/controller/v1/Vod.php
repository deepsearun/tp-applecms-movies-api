<?php

namespace app\controller\v1;

use app\BaseController;
use app\model\Vod as VodModel;
use app\validate\VodValidate;

/**
 * Class Vod
 * @package app\controller\v1
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Vod extends BaseController
{

    /**
     * 获取今日最新数据
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function today()
    {
        (new VodValidate())->goCheck('today');
        $data = (new VodModel())->getToday();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 获取影片列表
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list()
    {
        (new VodValidate())->goCheck('list');
        $data = (new VodModel())->list();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 获取幻灯图影片数据
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function slideShow()
    {
        $data = (new VodModel())->getSlideShow();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 获取热度较高影片
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function hits()
    {
        (new VodValidate())->goCheck('hits');
        $data = (new VodModel())->getHits();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 获取影片详情
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail()
    {
        (new VodValidate())->goCheck('detail');
        $data = (new VodModel())->detail();
        return self::showResCode('获取成功', $data);
    }

}