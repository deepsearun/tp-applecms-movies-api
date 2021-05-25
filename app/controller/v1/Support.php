<?php

namespace app\controller\v1;

use app\BaseController;
use app\validate\VodValidate;
use app\model\Vod;

/**
 * Class Support
 * @package app\controller\v1
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Support extends BaseController
{
    /**
     * 点赞影片
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function vod()
    {
        (new VodValidate())->goCheck('support');
        (new Vod())->support();
        return self::showResCodeWithOutData('ok');
    }
}