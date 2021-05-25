<?php

namespace app\controller\v1;

use app\BaseController;
use app\validate\SearchValidate;
use app\model\Search as SearchModel;
use app\model\Vod;
use app\model\Art;
use app\model\Topic;

/**
 * Class Search
 * @package app\controller\v1
 * @author Xiejiawei<forceboot@hotmail.com>
 */
class Search extends BaseController
{
    /**
     * 影片搜索关键词联想
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function vodThink()
    {
        (new SearchValidate())->goCheck();
        $data = (new Vod())->searchThink();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 文章搜索关键词联想
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function artThink()
    {
        (new SearchValidate())->goCheck();
        $data = (new Art())->searchThink();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 专题搜索关键词联想
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function topicThink()
    {
        (new SearchValidate())->goCheck();
        $data = (new Topic())->searchThink();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 搜索影片
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function vod()
    {
        (new SearchValidate())->goCheck();
        $data = (new Vod())->search();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 搜索文章
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function art()
    {
        (new SearchValidate())->goCheck();
        $data = (new Art())->search();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 搜索文章
     * @return \think\response\Json
     * @throws \app\lib\exception\BaseException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function topic()
    {
        (new SearchValidate())->goCheck();
        $data = (new Topic())->search();
        return self::showResCode('获取成功', $data);
    }

    /**
     * 热门搜索
     * @return \think\response\Json
     */
    public function hitsSearch()
    {
        $data = (new SearchModel())->getHits();
        return self::showResCode('获取成功', $data);
    }
}