<?php

namespace app\controller\v1;

use app\BaseController;
use app\validate\UlogVaildate;
use app\model\Ulog as UlogModel;

/**
 * Class Ulog
 * @package app\controller\v1
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Ulog extends BaseController
{
    /**
     * 获取用户影片播放记录
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function userVod()
    {
        $data =  (new UlogModel())->getVodPlayByUid();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 获取用户文章记录
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function userArt()
    {
        $data =  (new UlogModel())->getArtVisitByUid();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 获取用户专题记录
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function userTopic()
    {
        $data =  (new UlogModel())->getTopicVisitByUid();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 获取用户收藏记录
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function userVodCollect()
    {
        $data =  (new UlogModel())->getVodCollectByUid();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 获取用户影片点赞记录
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function userVodSupport()
    {
        $data =  (new UlogModel())->getVodSupportByUid();
        return self::showResCode('获取成功',$data);
    }

    /**
     * 创建影片历史记录
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createVod()
    {
        (new UlogVaildate())->goCheck('createVod');
        (new UlogModel())->addVodVisit();
        return self::showResCodeWithOutData('添加成功');
    }

    /**
     * 更新用户影片播放进度
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     */
    public function updateVodProgress()
    {
        (new UlogVaildate())->goCheck('updateVodProgress');
        (new UlogModel())->updateVodProgress();
        return self::showResCodeWithOutData('更新成功');
    }

    /**
     * 创建文章历史记录
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createArt()
    {
        (new UlogVaildate())->goCheck('createArt');
        (new UlogModel())->addArtVisit();
        return self::showResCodeWithOutData('添加成功');
    }

    /**
     * 创建专题历史记录
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createTopic()
    {
        (new UlogVaildate())->goCheck('createTopic');
        (new UlogModel())->addTopicVisit();
        return self::showResCodeWithOutData('添加成功');
    }

    /**
     * 删除记录
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete()
    {
        (new UlogVaildate())->goCheck('delete');
        (new UlogModel())->deleteById();
        return self::showResCodeWithOutData('删除成功');
    }

    /**
     * 删除多条记录
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function deletes()
    {
        (new UlogVaildate())->goCheck('deletes');
        (new UlogModel())->deleteByInId();
        return self::showResCodeWithOutData('删除成功');
    }
}