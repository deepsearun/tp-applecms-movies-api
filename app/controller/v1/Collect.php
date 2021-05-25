<?php


namespace app\controller\v1;

use app\BaseController;
use app\model\Vod;
use app\validate\VodValidate;

/**
 * Class Collect
 * @package app\controller\v1
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Collect extends BaseController
{
    /**
     * 收藏影片
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function vod()
    {
        (new VodValidate())->goCheck('collect');
        (new Vod())->collect();
        return self::showResCodeWithOutData('ok');
    }
}