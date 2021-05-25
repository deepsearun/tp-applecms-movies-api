<?php
namespace app\controller\v1;

use app\BaseController;
use app\validate\DeviceValidate;
use app\model\Device as DeviceModel;

/**
 * Class Device
 * @package app\controller\v1
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Device extends BaseController
{
    /**
     * 新增设备
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function create()
    {
        (new DeviceValidate())->goCheck();
        (new DeviceModel())->add();
        return self::showResCodeWithOutData('添加成功');
    }
}