<?php

namespace app\controller\v1;

use app\BaseController;

use app\model\Type as TypeModel;

use app\validate\TypeValidate;

/**
 * Class Type
 * @package app\controller\v1
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Type extends BaseController
{
    /**
     * 获取当前父分类
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getParentClass()
    {
        $data = (new TypeModel())->getParentClass();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 获取父分类下的子分类
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSonClassByParent()
    {
        $data = (new TypeModel())->getSonClassByParent();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 通过父分类筛选影片
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function screenVodByParentId()
    {
        (new TypeValidate())->goCheck('screenVodByParentId');
        $data = (new TypeModel())->screenVodByParentId();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 通过子分类筛选影片
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function screenVodBySonId()
    {
        (new TypeValidate())->goCheck('screenVodBySonId');
        $data = (new TypeModel())->screenVodBySonId();
        return self::showResCode('获取成功', $data);
    }
}