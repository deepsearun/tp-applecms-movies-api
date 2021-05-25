<?php

namespace app\controller\v1;

use app\BaseController;
use app\model\Art as ArtModel;
use app\validate\ArtValidate;

/**
 * Class Art
 * @package app\controller\v1
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Art extends BaseController
{
    /**
     * 获取资讯列表
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list()
    {
        (new ArtValidate())->goCheck('list');
        $data = (new ArtModel())->list();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 获取热门资讯
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function hits()
    {
        (new ArtValidate())->goCheck('hits');
        $data = (new ArtModel())->getHits();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 获取资讯详情
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function detail()
    {
        (new ArtValidate())->goCheck('detail');
        $data = (new ArtModel())->detail();
        return self::showResCode('获取成功', $data);
    }
}